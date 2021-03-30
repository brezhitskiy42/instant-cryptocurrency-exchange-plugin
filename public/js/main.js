jQuery(document).ready(function($) {

  'use strict';

  // Opening coins popup
  $('body').on('click', '.step1-block__ce .coin__ce, .step2-block__ce .coin__ce', function() {
    $.magnificPopup.open({
      items: {
        src: '#coins-popup__ce',
        type: 'inline'
      }
    });
  });

  // Opening bookmark popup
  $('body').on('click', 'button.bookmark__ce', function() {
    $.magnificPopup.open({
      items: {
        src: '#bookmark-popup__ce',
        type: 'inline'
      }
    });
  });

  // Searching coins
  $('input.search-coins__ce').on('change paste keyup', function() {

    var s = $(this).val();
    var coinsPopup = $('.coins-popup__ce');

    if (s == '') {
      coinsPopup.find('.coin__ce').show();
      return;
    }

    coinsPopup.find('.coin__ce').hide();

    s = s.toLowerCase();
    coinsPopup.find('.coin__ce[data-name*="'+ s +'"]').show();
    s = s.toUpperCase();
    coinsPopup.find('.coin__ce[data-symbol*="'+ s +'"]').show();

    coinsPopup.find('.coin__ce' + '.same__ce').hide();

  });

  // Selecting coin
  $('.coins-popup__ce .coin__ce').on('click', function() {

    var self = $(this);

    if (self.hasClass('unavailable__ce')) return;

    var step = self.parent().attr('data-step');
    var type = self.parent().attr('data-type');

    if (step == 'step1') {

      var coinIcon = self.find('img').attr('src');
      var coinName = self.find('.name__ce').text();
      var coinSymbol = self.attr('data-symbol');

      var coin = $('.step1-block__ce .coin__ce[data-type="'+ type +'"]');
      coin.find('img').attr('src', coinIcon);
      coin.find('.name__ce').text(coinName);
      coin.attr('data-symbol', coinSymbol);

    } else if (step == 'step2') {

      var step2Block = $('.step2-block__ce');
      var loader = step2Block.find('.loader__ce');

      var coinSymbol = self.attr('data-symbol');

      var from = step2Block.find('.deposit__ce .coin__ce').attr('data-symbol');
      var to = step2Block.find('.receive__ce .coin__ce').attr('data-symbol');
      var method = step2Block.attr('data-method');
      var bg = step2Block.attr('data-bg');
      var color = step2Block.attr('data-color');

      if (type == 'from') {
        from = coinSymbol;
      } else if (type == 'to') {
        to = coinSymbol;
      }

      if (!from || !to || !method || !bg || !color) return;

      loader.css('display', 'flex');

      var data = { action: 'load_pair_info', nonce_code: ajaxce.nonce, from: from, to: to };

      $.post(ajaxce.url, data, function(resp) {

        if (+resp === 0) {
          loader.hide();
          return;
        }

        var pairInfo = JSON.parse(resp);
        pairInfo.action = 'load_step2';
        pairInfo.nonce_code = ajaxce.nonce;
        pairInfo.from = from;
        pairInfo.to = to;
        pairInfo.method = method;
        pairInfo.bg = bg;
        pairInfo.color = color;

        $.post(ajaxce.url, pairInfo, function(resp) {

          if (+resp === 0) {
            loader.hide();
            return;
          }

          loader.hide();
          step2Block.replaceWith(resp);
          paintStep2Block();

        });

      });

    }

    $.magnificPopup.close();

    var alert = self.attr('data-alert');
    if (alert) {

      $('#alert-popup__ce').find('span.alert__ce').text(alert);

      setTimeout(function() {
        $.magnificPopup.open({
          items: {
            src: '#alert-popup__ce',
            type: 'inline'
          }
        });
      }, 1);

    }

  });

  $('body').on('click', '.step1-block__ce .coin__ce', function() {

    var type = $(this).attr('data-type');
    var coinSymbol = $('.step1-block__ce .coin__ce[data-type!="'+ type +'"]').attr('data-symbol');
    var coinsList = $('.coins-popup__ce .coins-list__ce');

    coinsList.attr('data-type', type);
    coinsList.attr('data-step', 'step1');
    $('.coins-popup__ce .coin__ce').removeClass('same__ce').show();
    $('.coins-popup__ce .coin__ce[data-symbol="'+ coinSymbol +'"]').addClass('same__ce').hide();

  });

  $('body').on('click', '.step2-block__ce .coin__ce', function() {

    var type = $(this).attr('data-type');
    var coinSymbol = $('.step2-block__ce .coin__ce[data-type!="'+ type +'"]').attr('data-symbol');
    var coinsList = $('.coins-popup__ce .coins-list__ce');

    coinsList.attr('data-type', type);
    coinsList.attr('data-step', 'step2');
    $('.coins-popup__ce .coin__ce').removeClass('same__ce').show();
    $('.coins-popup__ce .coin__ce[data-symbol="'+ coinSymbol +'"]').addClass('same__ce').hide();

  });

  // Swapping coins on step 1
  $('body').on('click', '.step1-block__ce .swap__ce i', function() {

    var step1Block = $('.step1-block__ce');

    var from = step1Block.find('.deposit__ce .coin__ce');
    var to = step1Block.find('.receive__ce .coin__ce');

    from.attr('data-type', 'to');
    to.attr('data-type', 'from');

    from.insertAfter('.receive__ce .title__ce');
    to.insertAfter('.deposit__ce .title__ce');

  });

  // Changing payment method
  $('body').on('click', '.step1-block__ce .deposit-receive__ce button', function() {

    var method = $(this).attr('data-method');
    $('.step1-block__ce input[name="method_ce"]').val(method);

    $('.step1-block__ce .deposit-receive__ce button').removeClass('active__ce');
    $(this).addClass('active__ce');

    paintStep1Block();

  });

  // Step 1 form handling
  $('body').on('submit', '.step1-block__ce form#step1__ce', function(e) {

    e.preventDefault();

    var self = $(this);
    self.next().css('display', 'flex');

    var from = self.find('.deposit__ce .coin__ce').attr('data-symbol');
    var to = self.find('.receive__ce .coin__ce').attr('data-symbol');
    var method = self.find('input[name="method_ce"]').val();
    var bg = self.parent().attr('data-bg');
    var color = self.parent().attr('data-color');

    if (!from || !to || !method || !bg || !color) {
      self.next().hide();
      return;
    }

    var data = { action: 'load_pair_info', nonce_code: ajaxce.nonce, from: from, to: to };

    $.post(ajaxce.url, data, function(resp) {

      if (+resp === 0) {
        self.next().hide();
        return;
      }

      var pairInfo = JSON.parse(resp);
      pairInfo.action = 'load_step2';
      pairInfo.nonce_code = ajaxce.nonce;
      pairInfo.from = from;
      pairInfo.to = to;
      pairInfo.method = method;
      pairInfo.bg = bg;
      pairInfo.color = color;

      $.post(ajaxce.url, pairInfo, function(resp) {

        if (+resp === 0) {
          self.next().hide();
          return;
        }

        self.next().hide();
        self.parent().replaceWith(resp);
        paintStep2Block();

      });

    });

  });

  // Back to step 1
  $('body').on('click', '.step2-block__ce .back__ce', function() {

    var step2Block = $('.step2-block__ce');
    var loader = step2Block.find('.loader__ce');

    loader.css('display', 'flex');

    var from = step2Block.find('.deposit__ce .coin__ce').attr('data-symbol');
    var to = step2Block.find('.receive__ce .coin__ce').attr('data-symbol');
    var method = step2Block.attr('data-method');
    var bg = step2Block.attr('data-bg');
    var color = step2Block.attr('data-color');

    if (!from || !to || !method || !bg || !color) {
      loader.hide();
      return;
    }

    var data = { action: 'back_to_step1', nonce_code: ajaxce.nonce, from: from, to: to, method: method, bg: bg, color: color };

    $.post(ajaxce.url, data, function(resp) {

      if (+resp === 0) {
        loader.hide();
        return;
      }

      loader.hide();
      step2Block.replaceWith(resp);
      paintStep1Block();

    });

  });

  // Swapping coins on step 2
  $('body').on('click', '.step2-block__ce .swap__ce', function() {

    var step2Block = $('.step2-block__ce');
    var loader = step2Block.find('.loader__ce');

    loader.css('display', 'flex');

    var from = step2Block.find('.receive__ce .coin__ce').attr('data-symbol');
    var to = step2Block.find('.deposit__ce .coin__ce').attr('data-symbol');
    var method = step2Block.attr('data-method');
    var bg = step2Block.attr('data-bg');
    var color = step2Block.attr('data-color');

    if (!from || !to || !method || !bg || !color) {
      loader.hide();
      return;
    }

    var data = { action: 'load_pair_info', nonce_code: ajaxce.nonce, from: from, to: to };

    $.post(ajaxce.url, data, function(resp) {

      if (+resp === 0) {
        loader.hide();
        return;
      }

      var pairInfo = JSON.parse(resp);
      pairInfo.action = 'load_step2';
      pairInfo.nonce_code = ajaxce.nonce;
      pairInfo.from = from;
      pairInfo.to = to;
      pairInfo.method = method;
      pairInfo.bg = bg;
      pairInfo.color = color;

      $.post(ajaxce.url, pairInfo, function(resp) {

        if (+resp === 0) {
          loader.hide();
          return;
        }

        loader.hide();
        step2Block.replaceWith(resp);
        paintStep2Block();

      });

    });

  });

  // Handling deposit amount
  $('body').on('change', '.step2-block__ce input[name="deposit_amount"]', function() {

    var self = $(this);

    var step2Block = $('.step2-block__ce');
    var minMaxFee = step2Block.find('.min-max-fee__ce');

    var amount = self.val();
    var min = self.attr('data-min');
    var max = self.attr('data-max');
    var rate = self.attr('data-rate');
    var fee = self.attr('data-fee');

    if (rate == 0) {
      step2Block.find('.error-info__ce').hide();
      step2Block.find('.error-info__ce.rate__ce').show();
      step2Block.find('button[type="submit"]').attr('disabled', true);

      minMaxFee.find('.item__ce.min__ce, .item__ce.max__ce').show();
      minMaxFee.find('.item__ce.give__ce, .item__ce.get__ce').hide();

      self.val('');
      self.next().val('');
      return;
    }

    if (!validator.isFloat(amount, { min: min })) {
      step2Block.find('.error-info__ce').hide();
      step2Block.find('.error-info__ce.min-limit__ce').show();
      step2Block.find('button[type="submit"]').attr('disabled', true);

      minMaxFee.find('.item__ce.min__ce, .item__ce.max__ce').show();
      minMaxFee.find('.item__ce.give__ce, .item__ce.get__ce').hide();

      self.val('');
      self.next().val('');
      return;
    }

    if (!validator.isFloat(amount, { max: max })) {
      step2Block.find('.error-info__ce').hide();
      step2Block.find('.error-info__ce.max-limit__ce').show();
      step2Block.find('button[type="submit"]').attr('disabled', true);

      minMaxFee.find('.item__ce.min__ce, .item__ce.max__ce').show();
      minMaxFee.find('.item__ce.give__ce, .item__ce.get__ce').hide();

      self.val('');
      self.next().val('');
      return;
    }

    var receiveAmount = amount * rate - fee;
    receiveAmount = parseFloat(receiveAmount.toFixed(8));
    self.next().val(receiveAmount);

    step2Block.find('.error-info__ce').hide();
    step2Block.find('button[type="submit"]').attr('disabled', false);

    minMaxFee.find('.item__ce.give__ce .count__ce span').text(Number(amount).toFixed(8));
    minMaxFee.find('.item__ce.get__ce .count__ce span').text(receiveAmount.toFixed(8));

    minMaxFee.find('.item__ce.min__ce, .item__ce.max__ce').hide();
    minMaxFee.find('.item__ce.give__ce, .item__ce.get__ce').show();

  });

  // Handling receive amount
  $('body').on('change', '.step2-block__ce input[name="receive_amount"]', function() {

    var self = $(this);

    var step2Block = $('.step2-block__ce');
    var minMaxFee = step2Block.find('.min-max-fee__ce');

    var amount = self.val();
    var min = self.attr('data-min');
    var max = self.attr('data-max');
    var rate = self.attr('data-rate');
    var fee = self.attr('data-fee');

    var receiveMin = min * rate - fee;
    var receiveMax = max * rate - fee;

    if (rate == 0) {
      step2Block.find('.error-info__ce').hide();
      step2Block.find('.error-info__ce.rate__ce').show();
      step2Block.find('button[type="submit"]').attr('disabled', true);

      minMaxFee.find('.item__ce.min__ce, .item__ce.max__ce').show();
      minMaxFee.find('.item__ce.give__ce, .item__ce.get__ce').hide();

      self.val('');
      self.prev().val('');
      return;
    }

    if (!validator.isFloat(amount, { min: receiveMin })) {
      step2Block.find('.error-info__ce').hide();
      step2Block.find('.error-info__ce.min-limit__ce').show();
      step2Block.find('button[type="submit"]').attr('disabled', true);

      minMaxFee.find('.item__ce.min__ce, .item__ce.max__ce').show();
      minMaxFee.find('.item__ce.give__ce, .item__ce.get__ce').hide();

      self.val('');
      self.prev().val('');
      return;
    }

    if (!validator.isFloat(amount, { max: receiveMax })) {
      step2Block.find('.error-info__ce').hide();
      step2Block.find('.error-info__ce.max-limit__ce').show();
      step2Block.find('button[type="submit"]').attr('disabled', true);

      minMaxFee.find('.item__ce.min__ce, .item__ce.max__ce').show();
      minMaxFee.find('.item__ce.give__ce, .item__ce.get__ce').hide();

      self.val('');
      self.prev().val('');
      return;
    }

    var depositAmount = (+amount + +fee) / rate;
    depositAmount = parseFloat(depositAmount.toFixed(8));
    self.prev().val(depositAmount);

    step2Block.find('.error-info__ce').hide();
    step2Block.find('button[type="submit"]').attr('disabled', false);

    minMaxFee.find('.item__ce.give__ce .count__ce span').text(depositAmount.toFixed(8));
    minMaxFee.find('.item__ce.get__ce .count__ce span').text(Number(amount).toFixed(8));

    minMaxFee.find('.item__ce.min__ce, .item__ce.max__ce').hide();
    minMaxFee.find('.item__ce.give__ce, .item__ce.get__ce').show();

  });

  // Updating data on step 2
  setInterval(function() {

    var step2Block = $('.step2-block__ce');
    var loader = step2Block.find('.loader__ce');

    if (!step2Block.length || loader.css('display') == 'flex') return;

    var from = step2Block.find('.deposit__ce .coin__ce').attr('data-symbol');
    var to = step2Block.find('.receive__ce .coin__ce').attr('data-symbol');
    var method = step2Block.attr('data-method');

    if (!from || !to || !method) return;

    var data = { action: 'load_pair_info', nonce_code: ajaxce.nonce, from: from, to: to };

    $.post(ajaxce.url, data, function(resp) {

      if (+resp === 0) return;

      var pairInfo = JSON.parse(resp);
      var rate = pairInfo.rate;
      var fee = pairInfo.minerFee;
      var max = pairInfo.limit;
      var min = pairInfo.minimum;

      var minMaxFee = step2Block.find('.min-max-fee__ce');

      step2Block.find('.rate__ce span.rate-amount__ce').text(rate);
      minMaxFee.find('.item__ce.min__ce .count__ce span').text(min);
      minMaxFee.find('.item__ce.max__ce .count__ce span').text(max);
      minMaxFee.find('.item__ce.fee__ce .count__ce span').text(fee);

      if (rate == 0) {
        step2Block.find('.error-info__ce').hide();
        step2Block.find('.error-info__ce.rate__ce').show();
        step2Block.find('button[type="submit"]').attr('disabled', true);
      }

      if (method == 'precise') {
        step2Block.find('.amount__ce input').attr('data-min', min).attr('data-max', max).attr('data-rate', rate).attr('data-fee', fee);
      }

    });

  }, 5000);

  // Step 2 form handling
  $('body').on('submit', '.step2-block__ce form#step2__ce', function(e) {

    e.preventDefault();

    var self = $(this);
    self.next().css('display', 'flex');

    var method = self.parent().attr('data-method');
    var depositAmount = self.find('input[name="deposit_amount"]');
    var receiveAmount = self.find('input[name="receive_amount"]');
    var destinationAddress = self.find('input[name="destination_address"]');
    var refundAddress = self.find('input[name="refund_address"]');

    var fromSymbol = self.find('.deposit__ce .coin__ce').attr('data-symbol');
    var toSymbol = self.find('.receive__ce .coin__ce').attr('data-symbol');
    var bg = self.parent().attr('data-bg');
    var color = self.parent().attr('data-color');

    if (method == 'quick') {

      var inputs = [destinationAddress, refundAddress];
      var error = false;

      for (var i = 0; i < 2; i++) {
        inputs[i].removeClass('input-error__ce');
        if (!inputs[i].val()) {
          inputs[i].addClass('input-error__ce');
          error = true;
        }
      }

      if (error) {
        self.find('.error-info__ce').hide();
        self.find('.error-info__ce.all-required__ce').show();
        self.next().hide();
        return;
      }

      var data = {
        action: 'create_quick_order',
        nonce_code: ajaxce.nonce,
        withdrawal: destinationAddress.val(),
        returnAddress: refundAddress.val(),
        fromSymbol: fromSymbol,
        toSymbol: toSymbol,
        bg: bg,
        color: color
      };

      $.post(ajaxce.url, data, function(resp) {

        if (+resp === 0) {
          self.find('.error-info__ce').hide();
          self.find('.error-info__ce.resp__ce').show();
          self.next().hide();
          return;
        }

        window.location.replace(resp);

      });

    } else if (method == 'precise') {

      var inputs = [depositAmount, receiveAmount, destinationAddress, refundAddress];
      var error = false;

      for (var i = 0; i < 4; i++) {
        inputs[i].removeClass('input-error__ce');
        if (!inputs[i].val()) {
          inputs[i].addClass('input-error__ce');
          error = true;
        }
      }

      if (error) {
        self.find('.error-info__ce').hide();
        self.find('.error-info__ce.all-required__ce').show();
        self.next().hide();
        return;
      }

      var data = {
        action: 'create_precise_order',
        nonce_code: ajaxce.nonce,
        withdrawal: destinationAddress.val(),
        returnAddress: refundAddress.val(),
        fromSymbol: fromSymbol,
        toSymbol: toSymbol,
        amount: receiveAmount.val(),
        bg: bg,
        color: color
      };

      $.post(ajaxce.url, data, function(resp) {

        if (+resp === 0) {
          self.find('.error-info__ce').hide();
          self.find('.error-info__ce.resp__ce').show();
          self.next().hide();
          return;
        }

        window.location.replace(resp);

      });

    }

  });

  // Attaching copy, tooltip and QR-code functionality
  new ClipboardJS('.copy-address__ce');
  new ClipboardJS('.copy-link__ce', { container: document.getElementById('bookmark-popup__ce') });
  tippy('.copy-address__ce, .copy-link__ce', { arrow: true, trigger: 'click' });
  new QRious({ element: document.getElementById('qr__ce'), value: $('.deposit-address__ce').text(), size: 200 });

  // Updating data on order status page
  setInterval(function() {

    var orderStatusBlock = $('.order-status-block__ce');
    var status = orderStatusBlock.attr('data-status');

    if (!orderStatusBlock.length || status == 'resolved' || status == 'complete') return;

    if (status == 'no_deposits' && orderStatusBlock.find('.no-deposits__ce.quick__ce').length) {

      var noDepositsQuick = orderStatusBlock.find('.no-deposits__ce.quick__ce');
      var from = noDepositsQuick.attr('data-from-symbol');
      var to = noDepositsQuick.attr('data-to-symbol');

      if ( ! from || ! to ) return;

      var data = { action: 'load_pair_info', nonce_code: ajaxce.nonce, from: from, to: to };

      $.post(ajaxce.url, data, function(resp) {

        if (+resp === 0) return;

        var pairInfo = JSON.parse(resp);
        var max = pairInfo.limit;
        var min = pairInfo.minimum;

        noDepositsQuick.find('span.from-amount__ce').text(min);
        noDepositsQuick.find('span.to-amount__ce').text(max);

      });

    }

    var currentRate = orderStatusBlock.find('.current-rate__ce');
    if (currentRate.length) {

      var from = currentRate.attr('data-from-symbol');
      var to = currentRate.attr('data-to-symbol');

      if ( ! from || ! to ) return;

      var data = { action: 'load_pair_info', nonce_code: ajaxce.nonce, from: from, to: to };

      $.post(ajaxce.url, data, function(resp) {

        if (+resp === 0) return;

        var pairInfo = JSON.parse(resp);
        var rate = pairInfo.rate;

        currentRate.find('span.rate-amount__ce').text(rate);

      });

    }

    var orderId = orderStatusBlock.attr('data-order-id');

    var data = {
      action: 'update_order',
      nonce_code: ajaxce.nonce,
      orderId: orderId,
      status: status
    };

    $.post(ajaxce.url, data, function(resp) {

      if (+resp === 0) return;

      orderStatusBlock.replaceWith(resp);
      paintOrderStatusBlock();

    });

  }, 5000);

  // Countdown functionality
  var countdown = $('#countdown__ce');
  if (countdown.length) {
    startTimer(countdown.attr('data-time'), countdown);
  }

  function startTimer(duration, display) {

    var timer = duration, minutes, seconds;

    var intervalId = setInterval(function () {
      minutes = parseInt(timer / 60, 10)
      seconds = parseInt(timer % 60, 10);

      minutes = minutes < 10 ? '0' + minutes : minutes;
      seconds = seconds < 10 ? '0' + seconds : seconds;

      display.text(minutes + ':' + seconds);

      if (minutes == '00' && seconds == '00') {
        clearInterval(intervalId);
        return;
      }

      if (--timer < 0) timer = duration;
    }, 1000);

  }

  // Changing transactions list size
  function changeTransactionsListSize() {

    var transactionsList = $('.transactions-list__ce');
    if (!transactionsList.length) return;

    var transactions = transactionsList.find('.transaction__ce');
    var transactionsListWidth = transactionsList.width();

    if (transactionsListWidth >= 750 && transactions.length > 1) {
      transactions.css('width', '50%');
    } else if (transactionsListWidth >= 1140 && transactions.length > 2) {
      transactions.css('width', '33.33%');
    } else {
      transactions.css('width', '100%');
    }

  }

  changeTransactionsListSize();

  $(window).on('resize', changeTransactionsListSize);

  // Setting transactions time
  function setTransactionsTime() {

    var transactionsList = $('.transactions-list__ce');
    if (!transactionsList.length) return;

    var transactions = transactionsList.find('.transaction__ce');
    transactions.each(function() {
      var time = $(this).find('.time__ce');
      var fromNow = moment.utc(time.attr('data-time'), 'YYYY-MM-DD HH:mm:ss').fromNow();
      time.text(fromNow);
    });

  }

  setTransactionsTime();

  // Reshaping time block
  function reshapeTimeBlock() {

    var transactionsList = $('.transactions-list__ce');
    if (!transactionsList.length) return;

    var reshape = false;
    var transactions = transactionsList.find('.transaction__ce');
    transactions.each(function() {

      var width = $(this).width();
      var infoWidth = $(this).find('.info__ce').innerWidth();
      var timeWidth = $(this).find('.time__ce').innerWidth();
      if (width < infoWidth + timeWidth) {
        reshape = true;
        return false;
      }

    });

    if (reshape) transactionsList.find('.time__ce').addClass('time-reshape__ce');
    else transactionsList.find('.time__ce').removeClass('time-reshape__ce');

  }

  reshapeTimeBlock();

  $(window).on('resize', reshapeTimeBlock);

  // Updating transactions list
  setInterval(function() {

    var transactionsBlock = $('.transactions__ce');
    if (!transactionsBlock.length) return;

    var count = transactionsBlock.attr('data-count'),
        type = transactionsBlock.attr('data-type'),
        bg = transactionsBlock.attr('data-bg'),
        color = transactionsBlock.attr('data-color');

    var data = {
      action: 'update_transactions_list',
      nonce_code: ajaxce.nonce,
      count: count,
      type: type,
      bg: bg,
      color: color
     };

    $.post(ajaxce.url, data, function(resp) {

      if (+resp === 0) return;

      transactionsBlock.replaceWith(resp);

      paintTransactions();
      setTransactionsTime();
      changeTransactionsListSize();
      reshapeTimeBlock();

    });

  }, 5000);

  // Painting blocks
  paintStep1Block();
  function paintStep1Block() {

    var $step1Block = $('.step1-block__ce');
    if (!$step1Block.length) return;

    var fg = $step1Block.attr('data-color'),
        bg = $step1Block.attr('data-bg');

    if (!fg || !bg) return;

    if (fg == '#55779f' && bg == '#ffffff') return;

    var fgSub = (tinycolor(fg).isLight()) ? '#333' : '#fff',
        bgSub = (tinycolor(bg).isLight()) ? '#333' : '#fff',
        fg2 = (tinycolor(fg).isLight()) ? tinycolor(fg).darken(10).toString() : tinycolor(fg).lighten(20).toString(),
        fg3 = (tinycolor(fg).isLight()) ? tinycolor(fg).darken(20).toString() : tinycolor(fg).lighten(10).toString(),
        fg3Sub = (tinycolor(fg3).isLight()) ? '#333' : '#fff';

    $step1Block.css({ background: bg, color: bgSub });
    $step1Block.find('.swap__ce').css({ color: bgSub });

    $step1Block.find('.deposit__ce button, .receive__ce button').css({ background: fg2, color: fg3Sub });
    $step1Block.find('.deposit__ce button.active__ce, .receive__ce button.active__ce').css({ background: fg3, color: fg3Sub });
    $step1Block.find('.next-btn__ce').css({ background: fg, color: fgSub });

    if (fg == '#55779f' && bg == '#000000') {
      $step1Block.find('.deposit__ce button, .receive__ce button').css({ color: bgSub });
      $step1Block.find('.deposit__ce button.active__ce, .receive__ce button.active__ce').css({ color: bgSub });
    }

  }

  function paintStep2Block() {

    var $step2Block = $('.step2-block__ce');
    if (!$step2Block.length) return;

    var fg = $step2Block.attr('data-color'),
        bg = $step2Block.attr('data-bg');

    if (!fg || !bg) return;

    if (fg == '#55779f' && bg == '#ffffff') return;

    var fgSub = (tinycolor(fg).isLight()) ? '#333' : '#fff',
        bgSub = (tinycolor(bg).isLight()) ? '#333' : '#fff',
        bgSubInverse = (tinycolor(bg).isLight()) ? '#fff' : '#000',
        fg2 = (tinycolor(fg).isLight()) ? tinycolor(fg).darken(5).toString() : tinycolor(fg).lighten(5).toString(),
        fg2Sub = (tinycolor(fg2).isLight()) ? '#333' : '#fff',
        fg2Sub2 = (tinycolor(fg2Sub).isLight()) ? 'rgba(255,255,255,0.5)' : '#000';

    $step2Block.find('.deposit-receive__ce, .amount__ce, .addresses__ce').css({ background: bg });
    $step2Block.find('.swap__ce, .addresses__ce label, .addresses__ce a').css({ color: bgSub });
    $step2Block.find('.amount__ce input, .addresses__ce input').css({ background: bg, color: bgSub, borderColor: fg });
    $step2Block.find('.amount__ce input, .addresses__ce input').on('focus', function() {
      $(this).css({ borderColor: fg2 });
    });
    $step2Block.find('.amount__ce input, .addresses__ce input').on('blur', function() {
      $(this).css({ borderColor: fg });
    });

    $step2Block.find('.rate__ce').css({ background: fg2, color: fg2Sub2 });
    $step2Block.find('.back__ce, .rate__ce span').css({ color: fg2Sub });
    $step2Block.find('.min-max-fee__ce').css({ background: fg });
    $step2Block.find('.min-max-fee__ce .title__ce').css({ color: fg2Sub2 });
    $step2Block.find('.min-max-fee__ce .count__ce').css({ color: fg2Sub });
    $step2Block.find('.next-btn__ce').css({ background: fg, color: fgSub });

  }

  paintOrderStatusBlock();
  function paintOrderStatusBlock() {

    var $orderStatusBlock = $('.order-status-block__ce');
    if (!$orderStatusBlock.length) return;

    $orderStatusBlock.find('p').children().unwrap();
    $orderStatusBlock.find('p').remove();

    var fg = $orderStatusBlock.attr('data-color'),
        bg = $orderStatusBlock.attr('data-bg');

    if (!fg || !bg) return;

    if (fg == '#55779f' && bg == '#ffffff') return;

    var fgSub = (tinycolor(fg).isLight()) ? '#333' : '#fff',
        bgSub = (tinycolor(bg).isLight()) ? '#333' : '#fff',
        fg2 = (tinycolor(fg).isLight()) ? tinycolor(fg).darken(5).toString() : tinycolor(fg).lighten(5).toString(),
        fg2Sub = (tinycolor(fg2).isLight()) ? '#333' : '#fff',
        fg2Sub2 = (tinycolor(fg2Sub).isLight()) ? 'rgba(255,255,255,0.5)' : '#000',
        bg2 = (tinycolor(bg).isLight()) ? tinycolor(bg).darken(10).toString() : tinycolor(bg).lighten(10).toString(),
        bg2Sub = (tinycolor(bg2).isLight()) ? '#333' : '#fff';

    $orderStatusBlock.find('.bookmark__ce, .content__ce').css({ background: bg, color: bgSub });
    $orderStatusBlock.find('.content__ce .main-text__ce .bold__ce').css({ color: bgSub });
    $orderStatusBlock.find('.content__ce .deposit-address__ce').css({ borderBottomColor: bgSub });
    $orderStatusBlock.find('.content__ce .time__ce, .content__ce .blockchain-link__ce').css({ background: bg2, color: bg2Sub });
    $orderStatusBlock.find('.progress__ce').css({ background: bg });
    $orderStatusBlock.find('.progress__ce .step1__ce, .progress__ce .step2__ce').css({ background: bg2 });

    $orderStatusBlock.find('.rate__ce').css({ background: fg2, color: fg2Sub2 });
    $orderStatusBlock.find('.rate__ce span').css({ color: fg2Sub });
    $orderStatusBlock.find('.id-bookmark__ce, .state-block__ce').css({ background: fg });
    $orderStatusBlock.find('.id-bookmark__ce .id__ce').css({ color: fgSub });
    $orderStatusBlock.find('.state-block__ce .item__ce').css({ color: fg2Sub2 });
    $orderStatusBlock.find('.state-block__ce .item__ce.active__ce').css({ color: fg2Sub });
    $orderStatusBlock.find('.state-block__ce .item__ce.done__ce').css({ color: bg2 });

  }

  paintTransactions();
  function paintTransactions() {

    var $transactions = $('.transactions__ce');
    if (!$transactions.length) return;

    var fg = $transactions.attr('data-color'),
        bg = $transactions.attr('data-bg');

    if (!fg || !bg) return;

    if (fg == '#55779f' && bg == '#ffffff') return;

    var fgSub = (tinycolor(fg).isLight()) ? '#333' : '#fff',
        bgSub = (tinycolor(bg).isLight()) ? '#333' : '#fff',
        fg2 = (tinycolor(fg).isLight()) ? tinycolor(fg).darken(10).toString() : tinycolor(fg).lighten(10).toString(),
        bg2 = (tinycolor(bg).isLight()) ? tinycolor(bg).darken(10).toString() : tinycolor(bg).lighten(10).toString(),
        bg2Sub = (tinycolor(bg2).isLight()) ? '#333' : '#fff',
        bg3 = (tinycolor(bg).isLight()) ? tinycolor(bg).darken(15).toString() : tinycolor(bg).lighten(15).toString();

    $transactions.css({ background: bg, color: bgSub });
    $transactions.find('.time__ce').css({ background: bg2, color: bg2Sub });
    $transactions.find('.transaction__ce').css({ borderTopColor: bg3, borderBottomColor: bg3 });

    $transactions.find('.header__ce').css({ background: fg, color: fgSub, borderTopColor: fg2, borderBottomColor: fg2 });
    $transactions.find('.currency__ce').css({ color: fg });

  }

});
