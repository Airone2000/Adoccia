<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\Search;
use App\Enum\FicheModeEnum;
use App\Form\CategoryType;
use App\Form\FicheType;
use App\Form\SaveSearchType;
use App\Form\SearchInCategoryType;
use App\Repository\CategoryRepository;
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
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category.index", methods={"GET"})
     * @inheritdoc
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
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
     * @Route("/{id}", name="category.show", methods={"GET"})
     * @inheritdoc
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category.edit", methods={"GET","POST"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @inheritdoc
     */
    public function edit(Request $request, Category $category): Response
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
     * @inheritdoc
     */
    public function editDraftForm(Category $category, FormHandlerInterface $formHandler, bool $new = false): Response
    {
        $formHandler->setDraftForm($category, $new);
        return $this->redirectToRoute('draftForm.edit', ['id' => $category->getDraftForm()->getId()]);
    }

    /**
     * @Route("/{id}", name="category.delete", methods={"DELETE"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
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
     * @Route("/{id}/add-fiche", methods={"get", "post"}, name="category.addFiche")
     * @IsGranted("ADD_FICHE_TO_CATEGORY", subject="category")
     * @inheritdoc
     */
    public function addFiche(Category $category, Request $request, FicheHandlerInterface $ficheHandler): Response
    {
        $form = $this->createForm(FicheType::class, null, [
            'category' => $category,
            'mode' => FicheModeEnum::EDITION
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
     * @inheritdoc
     */
    public function listFiches(Category $category): Response
    {
        return $this->render('category/list_fiches.html.twig', [
            'category' => $category,
            'fiches' => $category->getFiches()
        ]);
    }

    /**
     * @Route(
     *     path="/{categoryId}/fiches/search/{searchId}",
     *     methods={"get", "post"},
     *     name="category.searchFiches",
     *     defaults={"searchId" = 0}
     * )
     * @Entity(name="category", expr="repository.find(categoryId)")
     * @Entity(name="search", expr="repository.findOneByIdAndCategory(searchId, categoryId)")
     * @inheritdoc
     */
    public function advancedSearch(Category $category, Search $search = null, Request $request, CategoryFinderInterface $categoryFinder): Response
    {
        if ($search instanceof Search) {
            $results = $categoryFinder->search($category, $search->getCriterias());
            return $this->render('category/search_results.html.twig', [
                'results' => $results
            ]);
        }
        else {
            $form = $this->createForm(SearchInCategoryType::class, null, [
                'category' => $category
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $results = $categoryFinder->search($category, $form->getData());
                $formData = ['criterias' => json_encode($categoryFinder->getLastSearchCriterias())];
                $saveSearchForm = $this->createSaveSearchForm($category, $formData);

                return $this->render('category/search_results.html.twig', [
                    'results' => $results,
                    'saveSearchForm' => $saveSearchForm->createView()
                ]);
            }

            return $this->render('category/search.html.twig', [
                'category' => $category,
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route(
     *     path="/{id}/save-search",
     *     methods={"post"},
     *     name="category.saveSearch"
     * )
     * @inheritdoc
     */
    public function saveSearch(Category $category, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $form = $this->createSaveSearchForm($category);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $search = new Search();
                $search
                    ->setCriterias(json_decode($form->get('criterias')->getData(), true))
                    ->setCategory($category)
                    ->setUser($this->getUser())
                ;

                $em = $this->getDoctrine()->getManager();
                $em->persist($search);
                $em->flush();

                # Redirect to the search page
                return $this->redirectToRoute('category.searchFiches', [
                    'categoryId' => $category->getId(),
                    'searchId' => $search->getId()
                ]);
            }
        }

        return $this->redirectToRoute('category.show', ['id' => $category->getId()]);
    }

    private function createSaveSearchForm(Category $category, array $data = []): FormInterface
    {
        $saveSearchForm = $this->createForm(SaveSearchType::class, $data, [
            'action' => $this->generateUrl('category.saveSearch', ['id' => $category->getId()]),
            'method' => 'POST'
        ]);
        return $saveSearchForm;
    }

}
