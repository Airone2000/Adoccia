require('./../../css/form/edit.scss');
import $ from './../../vendor/jquery/jquery';
import Sortable from './../../vendor/Sortable/Sortable';

class Edit
{
    constructor() {
        this._makeItSortable();
        this._listenForResizeArea();
        this._listenForAddArea();
        this._listenForDeleteArea();
    }

    _makeItSortable() {
        let el = document.getElementById('form');
        new Sortable(el, {
            animation: 150,
            chosenClass: "sortable-chosen",
            dragClass: "sortable-drag",
            easing: "cubic-bezier(1, 0, 0, 1)",
            handle: '.handle',
            draggable: '.area'
        });
    }

    _listenForResizeArea() {
        $(document).on('change', '.resizer', function(){
            let $resizer = $(this);
            const selectedSize = +$resizer.val();
            const maxSize = +$resizer.prop('max');
            const minSize = +$resizer.prop('min');

            let size = selectedSize;

            size = size < minSize ? minSize : size;
            size = size > maxSize ? maxSize : size;
            $resizer.val(size);
            $resizer.parents('.area').css('width', 'calc('+size+'% - 20px)');
        });
    }

    _listenForAddArea() {
        $('.add-formArea').on('click', function(){
            let insertType = $(this).data('inserttype');
            let $newElement = $($('#sampleFormArea').html());
            if (insertType === 'prepend') {
                $('#form').prepend($newElement);
            }
            else {
                $('#form').append($newElement);
            }
        });
    }

    _listenForDeleteArea() {
        $(document).on('click', '.delete-formArea', function(){
            let $area = $(this).parents('.area');
            $area.css('opacity', 0);
            setTimeout(function(){
                $area.remove();
            }, 200);
        });
    }
}

new Edit();