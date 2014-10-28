var Questionary = (function() {

    function Questionary() {
        this.params = {
            ajaxFormClass: 'ajax-form',
            timeLeftHolder: 'time-left-holder'
        };
    }

    Questionary.prototype.init = function()
    {
        this.initAjaxForm();
    };

    Questionary.prototype.initAjaxForm = function()
    {
        var selfClass = this;

        var form = $('.' + selfClass.params.ajaxFormClass);

        var target = $('#' + form.data('target'));

        $('body').on('submit', '.' + selfClass.params.ajaxFormClass, function(e){

            e.preventDefault();

            var self = $(this);

            $(this).ajaxSubmit({
                //dataType: 'json',
                beforeSend: function () {

                },
                success: function(response) {
                    target.html(response);
                }
            });
        });
    };

    Questionary.prototype.initCountdown = function(selector, timeLeft, message)
    {
        var selfClass = this;

        var target = $('#' + selector + ' #' + selfClass.params.timeLeftHolder);

        var intervalElement;

        intervalElement = setInterval(function(){
            timeLeft -= 0.1;

            if (timeLeft <= 0) {
                clearInterval(intervalElement);
                alert(message);
                location.reload();
            } else {
                target.html(timeLeft.toFixed(1));
            }
        }, 100);
    };

    return new Questionary();
})();