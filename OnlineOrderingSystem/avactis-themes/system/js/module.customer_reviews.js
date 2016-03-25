$(function(){
    if (window.use_ajax_for_customer_reviews !== undefined && window.use_ajax_for_customer_reviews)
    {
        new AjaxManager({
            iSelector: '.button_add_review',
            instance: '.ProductAddReviewForm',
            updateAllInstances: true
        });
    }
});
