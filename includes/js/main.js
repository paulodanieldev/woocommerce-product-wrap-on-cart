// // jQuery( document ).ready(function() {
// //     var tp_req = jQuery('#woocommerce_iots_iots_requisition_type').val();

// //     if(tp_req =='order_id'){
// //         jQuery("#woocommerce_iots_iots_duplicate_id").removeAttr('disabled');
// //     }else{
// //         //jQuery("#woocommerce_iots_iots_duplicate_id").attr('disabled', 'disabled');
// //         jQuery('#woocommerce_iots_iots_duplicate_id').val('no');
// //     }
// // });

// // jQuery('#woocommerce_iots_iots_requisition_type').change(function(){
// //     var tp_req = jQuery(this).val();

// //     if(tp_req =='order_id'){
// //         jQuery("#woocommerce_iots_iots_duplicate_id").removeAttr('disabled');
// //     }else{
// //         jQuery("#woocommerce_iots_iots_duplicate_id").val('no');
// //         //jQuery("#woocommerce_iots_iots_duplicate_id").attr('disabled', 'disabled');
// //         //jQuery(this).removeAttr('checked');
        
// //     }
// // });

// function ic_add_wrap_to_cart(wrap_id, wrap_el){
//     jQuery.blockUI({message: null, overlayCSS: { backgroundColor: '#fff'} });
//     console.log('adiciona');
//     jQuery.post('/wp-admin/admin-ajax.php', {
//     action: 'woocommerce_ajax_add_to_cart',
//     ic_product_id: wrap_id
//     }, function(msg){
//         console.log(msg);
//         jQuery.unblockUI();
//         if (msg == 'true'){
//             jQuery("[name='update_cart']").prop("disabled", false);
//             jQuery("[name='update_cart']").trigger("click");
//         }else{
//             wrap_el.checked = false;
//             let divId = wrap_el.getAttribute("name").split('cart')[1].split('[')[1].replace("]", "");
//             jQuery('div#'+divId).show();
//             setTimeout(function(){ jQuery('div#'+divId).fadeOut("slow"); }, 5000);
//         }
//     })
// }

// function ic_remove_wrap_from_cart(wrap_id){
//     jQuery.blockUI({message: null, overlayCSS: { backgroundColor: '#fff'} });
//     console.log('remove');
//     jQuery.post('/wp-admin/admin-ajax.php', {
//         action: 'woocommerce_ajax_remove_from_cart',
//         ic_product_id: wrap_id
//     }, function(msg){
//         console.log(msg);
//         jQuery.unblockUI();
//         jQuery("[name='update_cart']").prop("disabled", false);
//         jQuery("[name='update_cart']").trigger("click");
//     })
// }

// function ic_change_wrap_checkbox(element_param){  
//     if (element_param.checked){
//         ic_add_wrap_to_cart(element_param.getAttribute("id"), element_param);
//     }else{
//         ic_remove_wrap_from_cart(element_param.getAttribute("id"));
//     }
// }