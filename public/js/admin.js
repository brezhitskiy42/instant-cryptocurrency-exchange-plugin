jQuery(document).ready(function($) {

  'use strict';

  // Replacing a standard select with Select2
  $('select[name="ce_option[from]"], select[name="ce_option[to]"], select.coin__ce').select2();

  // Replacing a standard table with DataTables
  $('#transactions-list__ce').DataTable({ responsive: true, order: [[5, 'desc']] });

  // Showing API response
  $('body').on('click', 'a.view-order__ce', function(e) {

    e.preventDefault();

    var orderId = $(this).attr('data-order-id');

    if (!orderId) return;

    $.magnificPopup.open({
      items: {
        src: '#view-api-response__ce',
        type: 'inline'
      }
    });

    var popup = $('.view-api-response__ce');
    var apiResponse = popup.find('.api-response__ce');
    var loader = popup.find('.loader__ce');

    apiResponse.hide();
    loader.css('display', 'flex');

    var data = { action: 'load_order_info', orderId: orderId };

    $.post(ajaxurl, data, function(resp) {

      if (+resp === 0) return;

      apiResponse.find('code').text(resp);
      Prism.highlightElement(document.getElementById('code__ce'));

      loader.hide();
      apiResponse.show();

    });

  });

  // Cleaning coin alert block
  function cleanCoinAlertBlock() {
    var table = $('.sample-coin-alert__ce').closest('table');
    table.find('th').hide();
    table.find('td').addClass('coin-alert-block__ce');
  }

  cleanCoinAlertBlock();

  // Filling a hidden field with coin alert info
  $('body').on('change', 'select.coin__ce', function() {
    var hiddenFieldName = 'ce_coins_alerts['+ $(this).val() +']';
    $(this).siblings('input[type="hidden"]').attr('name', hiddenFieldName);
  });

  $('body').on('change keyup paste', 'textarea.alert__ce', function() {
    $(this).siblings('input[type="hidden"]').attr('value', $(this).val());
  });

  // Removing coin alert
  $('body').on('click', 'span.remove-coin-alert__ce', function() {
    $(this).parent().remove();
  });

  // Adding new coin alert
  $('body').on('click', 'button.add-new-coin-alert__ce', function() {
    var sampleCoinAlert = $('.sample-coin-alert__ce');

    sampleCoinAlert.find('select.coin__ce').select2('destroy');

    var coinAlert = sampleCoinAlert.clone(true, true).removeClass('sample-coin-alert__ce');
    coinAlert.insertBefore(sampleCoinAlert);

    sampleCoinAlert.find('select.coin__ce').select2();
    coinAlert.find('select.coin__ce').select2();
    coinAlert.find('input[type="hidden"]').attr('name', 'ce_coins_alerts[BTC]');
  });

});
