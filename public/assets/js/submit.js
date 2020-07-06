/**
 * Submit form when choice button changes
 */
window.onload = function() {
    let choiceSelectField = document.getElementById('cc_pay_form_cardType');
    let formSubmitButton = document.getElementById('cc_pay_form_send');
    choiceSelectField.onchange = function() {
        formSubmitButton.click();
    };
};