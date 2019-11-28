<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\Search;
use App\Entity\User;
use App\Enum\FicheModeEnum;
use App\Form\CategorySearchType;
use App\Form\CategoryType;
use App\Form\FicheType;
use App\Form\SaveSearchType;
use App\Form\SearchInCategoryType;
use App\Repository\CategoryRepository;
use App\Repository\CategorySearchRepository;
use App\Repository\FicheRepository;
use App\Security\Voter\CategoryVoter;
use App\Services\CategoryFinder\CategoryFinderInterface;
use App\Services\CategoryHandler\CategoryHandlerInterface;
use App\Services\FicheHandler\FicheHandlerInterface;
use App\Services\FormHandler\FormHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories")
 */
class CategoryController extends AbstractController
{
    /**
     * List all categories : mine (no matter if published or not) + others (published)
     *
     * @Route("/", name="category.index", methods={"GET", "POST"})
     * @inheritdoc
     */
    public function index(CategoryRepository $categoryRepository, Request $request, CategorySearchRepository $categorySearchRepository): Response
    {
        # Get previous search for current user, or new search if none exist
        $categorySearch = $categorySearchRepository->findOneByUserOrGuestUniqueID($this->getUser(), $request->cookies->get('_guid'));
        $categorySearchForm = $this->createForm(CategorySearchType::class, $categorySearch);
        $categorySearchForm->handleRequest($request);

        # If the user perform a search, save it then redirect him to the same page
        if ($categorySearchForm->isSubmitted() && $categorySearchForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->detach($categorySearch);
            $em->persist($categorySearch);
            $em->flush();

            $url = $this->generateUrl('category.index');
            return $this->redirect($url);
        }

        # Load categories based on pagination
        /* @var User|null */
        $user = $this->getUser();
        $page = (int)$request->query->get('page', $categorySearch->getPage());
        $items = (int)$request->query->get('items', 30);
        $categories = $categoryRepository->findAllForUserOrPublic($user, $page, $items, $categorySearch);
        $totalItems = count($categories);
        $lastPage = ceil(($totalItems / $items));

        # If wanted page does not exist, redirect to the last one
        if ($page > $lastPage) {
            return $this->redirectToRoute('category.index', ['page' => $lastPage]);
        }

        # If page exists and the user has a search, update the page number
        # It can be new and non persisted in case where current user is not user
        # and he has not guestUniqueID
        if (!$categorySearch->isNew()) {
            $categorySearch->setPage($page);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'searchForm' => $categorySearchForm->createView(),
            'paginator' => ['currentPage' => $page, 'itemsPerPage' => $items, 'totalItems' => $totalItems, 'lastPage' => $lastPage]
        ]);
    }

    /**
     * Create a category
     *
     * @Route("/new", name="category.new", methods={"GET","POST"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') and user.hasPermission('CATEGORY_CREATE')")
     * @inheritdoc
     */
    public function new(Request $request, CategoryHandlerInterface $categoryHandler): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, [
            'validation_groups' => ['Category:Post', 'Category:Picture:Post']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryHandler->setCreatedBy($category, null, $autoPersist = true);
            return $this->redirectToRoute('category.index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Enter a category
     *
     * @Route("/{id}", name="category.show", methods={"GET"})
     * @Entity(name="category", expr="repository.getOneForUserById(null, id)")
     * @inheritdoc
     */
    public function show(Category $category): Response
    {
        if (CategoryVoter::canSeeCategory($this->getUser(), $category)) {
            /* @var User|null */
            return $this->render('category/show.html.twig', [
                'category' => $category,
            ]);
        }
        return $this->redirectToRoute('category.index');
    }

    /**
     * @Route("/{id}/edit", name="category.edit", methods={"GET","POST"})
     * @Entity(name="category", expr="repository.getOneForUserById(null, id)")
     * @IsGranted("EDIT_CATEGORY", subject="category")
     * @inheritdoc
     */
    public function edit(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'validation_groups' => ['Category:Put', 'Category:Picture:Put']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category.index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit-form/{new}", name="category.setAndEditDraftForm", methods={"get"})
     * @Entity(name="category", expr="repository.getOneForUserById(null, id)")
     * @IsGranted("EDIT_CATEGORY_FORM", subject="category")
     * @inheritdoc
     */
    public function editDraftForm(Category $category, FormHandlerInterface $formHandler, bool $new = false): Response
    {
        $formHandler->setDraftForm($category, $new);
        return $this->redirectToRoute('draftForm.edit', ['id' => $category->getDraftForm()->getId()]);
    }

    /**
     * @Route("/{id}", name="category.delete", methods={"DELETE"})
     * @Entity(name="category", expr="repository.getOneForUserById(null, id)")
     * @IsGranted("DELETE_CATEGORY", subject="category")
     * @inheritdoc
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category.index');
    }

    /**
     * @Route("/{id}/fiches/add-single", methods={"get", "post"}, name="category.addFiche")
     * @Entity(name="category", expr="repository.getOneForUserById(null, id)")
     * @IsGranted("ADD_FICHE_TO_CATEGORY", subject="category")
     * @inheritdoc
     */
    public function addFiche(Category $category, Request $request, FicheHandlerInterface $ficheHandler): Response
    {
        $fiche = new Fiche();
        $fiche->setCreator($this->getUser());

        $form = $this->createForm(FicheType::class, null, [
            'category' => $category,
            'mode' => FicheModeEnum::EDITION,
            'fiche' => $fiche
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $data['category'] = $category;
                $fiche = $ficheHandler->createFicheFromFicheTypeData($data);
                return $this->redirectToRoute('fiche.show', ['id' => $fiche->getId()]);
            }
            catch (\Exception $e) {
                $this->addFlash('addFicheError', '');
            }
        }

        return $this->render('category/add_fiche.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'fiche' => new Fiche()
        ]);
    }

    /**
     * @Route(
     *     path="/{id}/fiches",
     *     methods={"get"},
     *     name="category.listFiches"
     * )
     * @Entity(name="category", expr="repository.getOneForUserById(null, id)")
     * @inheritdoc
     */
    public function listFiches(Category $category, FicheRepository $ficheRepository, Request $request): Response
    {
        if (CategoryVoter::canListCategoryFiches($this->getUser(), $category)) {
            $page = (int)$request->query->get('page', 1);
            $items = (int)$request->query->get('items', 30);
            $fiches = $ficheRepository->findAllForCategoryAndUser($category, $this->getUser(), $page, $items);
            return $this->render('category/list_fiches.html.twig', [
                'category' => $category,
                'fiches' => $fiches
            ]);
        }

        return $this->redirectToRoute('category.index');
    }

    /**
     * @Route(
     *     path="/{categoryId}/search/{searchId}",
     *     methods={"get", "post"},
     *     name="category.searchFiches",
     *     defaults={"searchId" = 0}
     * )
     * @Entity(name="category", expr="repository.getOneForUserById(null, categoryId)")
     * @Entity(name="search", expr="repository.findOneByIdAndCategory(searchId, categoryId)")
     * @inheritdoc
     */
    public function advancedSearch(Category $category, Search $search = null, Request $request, CategoryFinderInterface $categoryFinder): Response
    {
        if (CategoryVoter::canSearchInCategory($this->getUser(), $category)) {

            # Fill the form with existing data from Search
            $search = $search ?? new Search();

            $form = $this->createForm(SearchInCategoryType::class, $search->getCriterias(), [
                'category' => $category
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                # AutoSave the search
                $search = new Search();
                $search
                    ->setCriterias($form->getData())
                    ->setCategory($category)
                    ->setUser($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($search);
                $em->flush();

                # And then, redirect to results
                return $this->redirectToRoute('category.searchResults', [
                    'categoryId' => $category->getId(),
                    'searchId' => $search->getId()
                ]);
            }

            return $this->render('category/search.html.twig', [
                'category' => $category,
                'form' => $form->createView(),
                'search' => $search
            ]);
        }
        return $this->redirectToRoute('category.index');
    }

    /**
     * @Route(
     *     path="/{categoryId}/fiches/{searchId}/results",
     *     methods={"get"},
     *     name="category.searchResults"
     * )
     * @Entity(name="category", expr="repository.getOneForUserById(null, categoryId)")
     * @Entity(name="search", expr="repository.findOneByIdAndCategory(searchId, categoryId)")
     * @inheritdoc
     */
    public function searchResults(Category $category, Search $search, CategoryFinderInterface $categoryFinder): Response
    {
        if (CategoryVoter::canSearchInCategory($this->getUser(), $category)) {
            $results = $categoryFinder->search($category, $search->getCriterias());
            return $this->render('category/search_results.html.twig', [
                'results' => $results,
                'category' => $category,
                'search' => $search
            ]);
        }
        return $this->redirectToRoute('category.index');
    }

}
