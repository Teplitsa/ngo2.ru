jQuery(document).ready(function($){

    $(document).on('submit', 'form.leyka-pm-form', function(e){

        e.preventDefault();

        // Donation form validation is already passed in the main script (public.js)

        var $form = $(this),
            data = $form.serialize();

        data.action = 'leyka_donation_submit';

        console.log(leyka, data)

        $.ajax({
            type: 'post',
            url: leyka.ajaxurl,
            data: data,
            beforeSend: function(xhr){
                /** @todo Show some loader */
            }
        }).done(function(response){

            if( !response ) {
                /** @todo Show some error message on the form */
                return false;
            }

            var widget = new cp.CloudPayments();
            widget.charge({
                publicId: 'pk_c5fcab988a7b37471933c466a4432',
                description: 'Пример оплаты (деньги сниматься не будут)', // назначение
                amount: 10,
                currency: 'RUB',
                invoiceId: '1234567', // номер заказа  (необязательно)
                accountId: 'user@example.com', // идентификатор плательщика (необязательно)
                data: {
                    myProp: 'myProp value'
                }
            }, function (options) { // success callback

            }, function (reason, options) { // fail callback

            });
        });
    });
});