// Dirty quick fixes for core display changes
(function ($) {
    $(function () {
        // Always remove the info button (this is core admin functionality)
        $('.icon-actions-document-info').parent('.btn').remove();

        // Always remove empty fillers for this view
        $('.icon-state-default.icon-empty-empty').parent('.btn').remove();

        // Always remove "Both" and "Admin only" from select
        $('select#tx_Beuser_usertype option[value="0"], select#tx_Beuser_usertype option[value="1"]').remove();

        // Remove icon per access
        var noAccess = $('meta[property=extension-no-access]').attr('content');
        if (noAccess && noAccess.length > 0 && noAccess !== 'all') {
            var hide = noAccess.split(',');
            hide.forEach(function(element) {
                switch (element) {
                    case 'action-edit':
                        $('.icon-actions-document-open').parent('.btn').remove();
                        break;
                    case 'action-hide':
                        $('.icon-actions-edit-hide, .icon-actions-edit-unhide').parent('.btn').remove();
                        break;
                    case 'action-delete':
                        $('.icon-actions-edit-delete').parent('.btn').remove();
                        break;
                    case 'action-switch-user':
                        $('.icon-actions-system-backend-user-switch').parent('.btn').remove();
                        break;
                }
            });
        }

    });
})(jQuery);
