function fs_transliteration(t){t=t.toLowerCase();for(var a={"а":"a","б":"b","в":"v","г":"g","д":"d","е":"e","ё":"e","ж":"zh","з":"z","и":"i","й":"j","к":"k","л":"l","м":"m","н":"n","о":"o","п":"p","р":"r","с":"s","т":"t","у":"u","ф":"f","х":"h","ц":"c","ч":"ch","ш":"sh","щ":"sh","ъ":"-","ы":"y","ь":"-","э":"e","ю":"yu","я":"ya"," ":"-",_:"-","`":"-","~":"-","!":"-","@":"-","#":"-",$:"-","%":"-","^":"-","&":"-","*":"-","(":"-",")":"-","-":"-","=":"-","+":"-","[":"-","]":"-","\\":"-","|":"-","/":"-",".":"-",",":"-","{":"-","}":"-","'":"-",'"':"-",";":"-",":":"-","?":"-","<":"-",">":"-","№":"-"},e="",r="",n=0;n<t.length;n++)void 0!=a[t[n]]?r==a[t[n]]&&"-"==r||(e+=a[t[n]],r=a[t[n]]):(e+=t[n],r=t[n]);return e=TrimStr(e)}function TrimStr(t){return(t=t.replace(/^-/,"")).replace(/-$/,"")}jQuery(document).on("click","[data-fs-action='modal']",function(t){t.preventDefault();var a=jQuery(this).attr("href");jQuery(a).fadeIn()}),jQuery(document).on("click","[data-fs-action='modal-close']",function(t){t.preventDefault();var a=jQuery(this).parents(".fs-modal");jQuery(a).fadeOut()}),jQuery('[data-action="change-attr"]').on("change",function(){jQuery(this);var t=jQuery(this).data("target"),a=jQuery(this).data("product-id"),e=jQuery("#fs-atc-"+a).data("attr"),r=jQuery(this).attr("name");jQuery(t).val(jQuery(this).val()),e.terms=[],jQuery('[name="'+r+'"]').each(function(t){jQuery(this).prop("checked",!1)}),jQuery(this).prop("checked",!0),jQuery('[data-action="change-attr"]').each(function(t){jQuery(this).prop("checked")&&jQuery(this).val()&&(e.terms[t]=jQuery(this).val())}),Array.prototype.clean=function(t){for(var a=0;a<this.length;a++)this[a]==t&&(this.splice(a,1),a--);return this},e.terms.clean(void 0),jQuery("#fs-atc-"+a).attr("data-attr",JSON.stringify(e))}),jQuery("[data-action=add-to-cart]").on("click",function(t){t.preventDefault();var a=!0;if(jQuery('[name="fs-attr"]').each(function(){if(""==jQuery(this).val()){a=!1;var t=new CustomEvent("fs_no_selected_attr");document.dispatchEvent(t)}}),!a)return a;var e=jQuery(this),r=e.data("product-id"),n=e.data("attr"),i={button:e,id:r,name:e.data("product-name"),attr:n,image:e.data("image"),success:!0,text:{success:e.data("success"),error:e.data("error")}},u={action:"add_to_cart",attr:n,post_id:r};jQuery.ajax({url:FastShopData.ajaxurl,data:u,beforeSend:function(){var t=new CustomEvent("fs_before_add_product",{detail:i});return document.dispatchEvent(t),t.success}}).done(function(t){var a=new CustomEvent("fs_add_to_cart",{detail:i});document.dispatchEvent(a)})}),jQuery('[data-fs-action="wishlist"]').on("click",function(t){t.preventDefault();var a=jQuery(this).data("product-id"),e=jQuery(this).data("name"),r=jQuery(this);jQuery.ajax({url:FastShopData.ajaxurl,data:{action:"fs_addto_wishlist",product_id:a},beforeSend:function(){var t=new CustomEvent("fs_before_to_wishlist",{detail:{id:a,image:r.data("image"),name:e,button:r}});document.dispatchEvent(t)}}).done(function(t){var n=jQuery.parseJSON(t),i=new CustomEvent("fs_add_to_wishlist",{detail:{id:a,name:e,button:r,image:r.data("image"),ajax_data:n}});document.dispatchEvent(i)})});