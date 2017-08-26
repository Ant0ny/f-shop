jQuery(function($){
    // тип атрибута в редкатировании атрибутов
    $(".fs-color-select").spectrum({
        color: $(this).val(),
        showInput: true,
        className: "full-spectrum",
        showInitial: true,
        showPalette: true,
        showSelectionPalette: true,
        maxSelectionSize: 10,
        preferredFormat: "hex",
        localStorageKey: "spectrum.demo",
        palette: [
        ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
        "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
        ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
        "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
        ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
        "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
        "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
        "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
        "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
        "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
        "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
        "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
        "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
        "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
        ]
    });

    //показываем скррываем кнопку загрузки изображения в зависимости от типа добавляемого атрибута
    $('#fs_att_type').on('change', function(event) {
        event.preventDefault();
        $('.fs-att-values').css({'display':'none'});
        switch ($(this).val()){
            case "color":
            $('#fs-att-color').css({'display':'table-row'});
            break;
            case "image":
            $('#fs-att-image').css({'display':'table-row'});
            break;
        }

    });
    //вызываем стандартный загрузчик изображений
    $('.select_file').on('click',function(){
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(this);
        wp.media.editor.open(button);
        wp.media.editor.send.attachment = function(props, attachment) {
            $(button).next().val( attachment.id);
            $(button).prev().css({
                'background-image': 'url('+attachment.url+')'
            });
            $(button).text('изменить изображение');
            wp.media.editor.send.attachment = send_attachment_bkp;
            button.parents('.fs-fields-container').find('.delete_file').fadeIn(400);
        };

        return false;
    });
    $('.delete_file').on('click',function () {
        if(confirm('Вы точно хотите удалить изображение?')){
            $(this).parents('.fs-fields-container').find('input').val('');
            $(this).parents('.fs-fields-container').find('.fs-selected-image').css({
                'background-image': 'none'
            });
            $(this).parents('.fs-fields-container').find('.select_file').text('выбрать изображение');
            $(this).fadeOut(400);

        }

    });



    /*
     * действие при нажатии на кнопку загрузки изображения
     * вы также можете привязать это действие к клику по самому изображению
     */
     $('.upload-mft').live('click',function(){
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(this);
        wp.media.editor.send.attachment = function(props, attachment) {
            $(button).parents('.mmf-image').find('.img-url').val( attachment.id);
            $(button).parents('.mmf-image').find('.image-preview').attr( 'src',attachment.url);

            $(button).prev().val(attachment.id);
            wp.media.editor.send.attachment = send_attachment_bkp;
        };
        wp.media.editor.open(button);
        return false;
    });
    /*
     * удаляем значение произвольного поля
     * если быть точным, то мы просто удаляем value у input type="hidden"
     */
     $('.remove_image_button').click(function(){
        var r = confirm("Уверены?");
        if (r == true) {
            var src = $(this).parent().prev().attr('data-src');
            $(this).parent().prev().attr('src', src);
            $(this).prev().prev().val('');
        }
        return false;
    });

     var nImg='<div class="mmf-image"><img src="" alt="" width="164" height="133" class="image-preview"><input type="hidden" name="fs_galery[]" value="" class="img-url"><button type="button" class="upload-mft">Загрузить</button><button type="button" class="remove-tr" onclick="btn_view(this)">удалить</button></div>';
     jQuery('#new_image').click(function(event) {
        event.preventDefault();
        if (jQuery('#mmf-1 .mmf-image').length>0) {
            jQuery('#mmf-1 .mmf-image:last').after(nImg);
        }else{
            jQuery('#mmf-1').html(nImg);
        }
    });
 });

function btn_view(e) {
    jQuery(e).parents('.mmf-image').remove();
}

jQuery(document).ready(function($) {
    // вкладки на странице настроек товара
    $( "#fs-tabs" ).tabs( {
       active   : $.cookie('postactivetab'),
       activate : function( event, ui ){
        $.cookie( 'postactivetab', ui.newTab.index(),{
            expires : 10
        });
    }
}).addClass( "ui-tabs-vertical ui-helper-clearfix" );
    
    $( "#fs-tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
    // вкладки на странице настроек магазина
    $( "#fs-options-tabs" ).tabs({
      active   : $.cookie('activetab'),
      activate : function( event, ui ){
        $.cookie( 'activetab', ui.newTab.index(),{
            expires : 10
        });
    }
}).addClass( "ui-tabs-vertical ui-helper-clearfix" );
    $( "#fs-options-tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
    $(".fs-metabox input[type='radio']").checkboxradio();

    //действия в админке
    $('[data-fs-action*=admin_]').on('click',function(event) {
        event.preventDefault();
        var thisButton=$(this);
        var buttonContent=$(this).text();
        var buttonPreloader='<img src="/wp-content/plugins/f-shop/assets/img/preloader-1.svg">';

        if ($(this).data('fs-confirm').length>0) {
            if (confirm($(this).data('fs-confirm'))) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    beforeSend:function() {
                        thisButton.find('div').remove();
                        thisButton.html(buttonPreloader+buttonContent);
                    },
                    data: {action:$(this).data('fs-action')},
                })
                .done(function(result) {
                   result=jQuery.parseJSON(result);
                   thisButton.find('img').fadeOut(600).remove();
                   if (result.status==true) {
                    thisButton.html('<div class="success">'+result.message+'</div>'+buttonContent);
                    if (result.action=='refresh') {
                        setTimeout(function(){ location.reload();  },2000);
                    }
                }else{
                    thisButton.html('<div class="error">'+result.message+'</div>'+buttonContent);
                }
            })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
                
            }
        }
        
    });
    $('[data-fs-action="enabled-select"]').on('click', function(event) {
        event.preventDefault();
        $(this).next().fadeIn();
    });    
    $('#tab-4').on('change','[data-fs-action="select_related"]', function(event) {
        event.preventDefault();
        var thisVal=$(this).val();
        var text;
        $(this).find('option').each(function(index, el) {
            if (thisVal==$(this).attr('value')) {
             text =$(this).text();
         }
         
         
     });
        $('#tab-4 .related-wrap').append('<li class="single-rel"><span>'+text+'</span> <button type="button" data-fs-action="delete_parents" class="related-delete" data-target=".single-rel">удалить</button><input type="hidden" name="fs_related_products[]" value="'+thisVal+'"></li>');
        $(this).fadeOut().remove();

    });
    $('body').on('click', '[data-fs-action="delete_parents"]', function(event) {
     $(this).parents($(this).data('target')).remove();
 });
    // получаем посты термина во вкладке связанные в редактировании товара
    $('#tab-4').on('change','[data-fs-action="get_taxonomy_posts"]',function(event) {
        var term=$(this).val();
        var thisSel=$(this);
        var postExclude=$(this).data('post');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {action: 'fs_get_taxonomy_posts','term_id':term,'post':postExclude},
        })
        .done(function(data) {
            var json=$.parseJSON(data);
            thisSel.prop('selectedIndex',0);
            thisSel.hide();
            thisSel.parent().append(json.body);
        });
        
    });

    $( ".fs-sortable-items" ).sortable();

});


