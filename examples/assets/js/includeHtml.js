/**
 * Add HTML form attribute include-html to existing page.
 */
function includeHTML()
{
    let z, i, elmnt, file, xhttp;
    /*loop through a collection of all HTML elements:*/
    z = document.getElementsByTagName("*");
    for (i = 0; i < z.length; i++) {
        elmnt = z[i];
        /*search for elements with a certain atrribute:*/
        file = elmnt.getAttribute("include-html");
        if (file) {
            /*make an HTTP request using the attribute value as the file name:*/
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState === 4) {
                    if (this.status === 200) {
                        elmnt.innerHTML = this.responseText;
                    }
                    if (this.status === 404) {
                        elmnt.innerHTML = "Page not found.";
                    }
                    /*remove the attribute, and call this function once more:*/
                    elmnt.removeAttribute("include-html");
                    includeHTML();
                }
            };
            xhttp.open("GET", file, true);
            xhttp.send();
            /*exit the function:*/
            return;
        }
    }
}

/**
 * Include all payment menus
 */
function includePaymentMenu()
{
    /**
     * List of all available payment menus
     * It should be the same as order name in payments folder
     * @type {string[]}
     */
    let payments = [
        'AlipayCrossborder',
        'Bancontact',
        'CreditCard',
        'CreditCardMoto',
        'Eps',
        'Giropay',
        'iDEAL',
        'Maestro',
        'Masterpass',
        'PayByBankApp',
        'PoiPia',
        'PayPal',
        'Payolution_BtwoB',
        'Payolution_Invoice',
        'Paysafecard',
        'Przelewy24',
        'RatePAY_DirectDebit',
        'RatePAY_Installment',
        'RatePAY_Invoice',
        'SepaDirectDebit',
        'SepaCredit',
        'SepaBtwoB',
        'Sofort',
        'UPOP',
        'UnionpayInternational',
        'WeChat'
    ];

    let element = document.getElementById('payments');
    let renderedHtml = '';
    payments.forEach(function (payment, index) {
        let file = 'payments/' + payment + '/menu.html';
        if (index % 3 === 0) {
            renderedHtml += '<div class="row">';
        }
        renderedHtml += '<div include-html="' + file + '"></div>';
        if (index % 3 === 2 || index === (payments.length - 1)) {
            renderedHtml += '</div>';
        }
    });
    element.innerHTML = renderedHtml;
}
