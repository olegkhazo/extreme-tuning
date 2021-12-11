window.onload = function() {

  // Checks input 'Вага, кг' on 'Нові відправлення' page on zero.
  function checkInvoiceCargoMass(e) {
    let input = document.getElementById('invoice_cargo_mass');
    if (input.value == 0) {
      input.setCustomValidity('Вага не може бути 0. Введіть вагу відправлення.');
    } else {
      input.setCustomValidity(''); // input is fine -- reset the error message
    }
  }
  if ( "morkvaup_invoice" == location.search.split('page=')[1] && document.querySelector('.checkforminputs') ) {
    document.querySelector('.checkforminputs').addEventListener('click', checkInvoiceCargoMass, true);
  }

  function checkSenderCyrillic(e) {
    const cyrillicPattern = /^[\u0400-\u04FF']+(?:-[\u0400-\u04FF']+)*$/;
    var valSender = jQuery('#sender_first_name').val();
    if (!cyrillicPattern.test(valSender) && valSender!==undefined) {
      e.preventDefault();
      alert('Ім\`я відправника треба писати кирилицею.\nВиправіть це та повторіть спробу.');
    }
  }

  function checkRecipientCyrillic(e) {
    const cyrillicPattern = /^[\u0400-\u04FF']+(?:-[\u0400-\u04FF']+)*$/;
    var valRec = jQuery('#rec_first_name').val();
    if (!cyrillicPattern.test(valRec) && valRec!==undefined) {
      e.preventDefault();
      alert('Прізвище одержувача треба писати кирилицею.\nВиправіть це та повторіть спробу.');
    }
  }

  var sp1 = document.getElementById("sp1");
  if (sp1) {
    sp1.addEventListener("click", function() {
      textareavalue = jQuery('#td45').val();
      var va = 'p';
      jQuery('#td45').val(textareavalue + ' [' + va + ']')
    });

    jQuery("select#shortselect").change(function() {
      textareavalue = jQuery('#td45').val();
      va = jQuery(this).val();
      jQuery('#td45').val(textareavalue + ' [' + va + ']')
    });

    jQuery("select#shortselect2").change(function() {
      textareavalue = jQuery('#td45').val();
      va = jQuery(this).val();
      jQuery('#td45').val(textareavalue + ' [' + va + ']')

    });


  }


  jQuery(function() {

    jQuery('.formsubmitup').on('click', function(e) {
      att = jQuery(this).attr('alert');
      if (att != '') {
        alert(att);
      }

      jQuery(this).parent().trigger("submit");
    });
    jQuery('.handlediv').on('click', function(e) { //when content of metabox couldnt be open
      //jQuery(this).parent().toggleClass('closed');
      aria = jQuery(this).attr('aria-expanded');
      if (aria == 'true') {
        //jQuery(this).attr('aria-expanded', 'false');
      } else {
        //jQuery(this).attr('aria-expanded', 'true');
      }
    });


    jQuery('#invoice_other_fields .insideup .button').on('click', function(e) {

        text = jQuery(this).text();
        console.log(text);
        if(text == ' Друк накладної'){
          text = 'Ви дійсно бажаєте друкувати накладну';
          console.log('text1');
        }
        if(text == ' Друк стікера'){
          text = 'Ви дійсно бажаєте друкувати стікер';
          console.log('text2');
        }
        if(text == 'Відпралення...'){
          text = 'Ви дійсно бажаєте Відправити на e-mail';
          console.log('text3');
        }
             if(!confirm(text + '?')){
                 e.preventDefault();
                 alert("Операцію відхилено");
             }
      });
      if (jQuery('#MyDate').length > 0) {//fix adminbar button freeze on page with jquery datepicker

          jQuery('#MyDate').datepicker();
          jQuery('#MyDate').datepicker("option", "dateFormat", "dd.mm.yy");

        wpbar = document.getElementById('wp-admin-bar-menu-toggle');
        wpbar.addEventListener('click', function(){
          document.getElementById('wpwrap').classList.toggle('wp-responsive-open');
        });
      }

      textw = "Ваше замовлення #[NOVAPOSHTA_ORDER] вже сформоване і буде відправлено [NOVAPOSHTA_DATE] Новою Поштою. \n\nНомер накладної\: [NOVAPOSHTA_TTN]";
      jQuery("#morkvanp_email_editor_id").text(textw);
      if ( jQuery('#mceu_24').length > 0 ){
        jQuery('#mceu_24').hide();
        jQuery('#morkvanp_email_editor_id').show();
        //jQuery('#wp-morkvanp_email_editor_id-editor-tools').hide();
      }
    });

   var MyDiv1 = document.getElementById("messagebox");
    if(MyDiv1){
        var h = MyDiv1.getAttribute('data');
        //h-=20;
        //var MyDiv2 = document.getElementById('messagebox');
        //MyDiv2.innerHTML = MyDiv1.innerHTML;
        //MyDiv2.style.height = h + 'px';
        MyDiv1.style.height = h + 'px';
				MyDiv1.style.padding = 8 + 'px';
        //MyDiv1.childNodes[0].style.padding = 0 ;
        //MyDiv2.classList.add('error');
    }

    var MyDiv3 = document.getElementById("nnnid");
    if(MyDiv3){
        MyDiv3 = document.getElementById("nnnid");
        var h = 182 + 'px';
        console.log(h);
        var MyDiv4 = document.getElementById('messagebox');
        MyDiv4.innerHTML = MyDiv3.innerHTML;
        MyDiv4.style.height = h;
        MyDiv4.style.padding = '8px';
        MyDiv4.classList.add('updated');

    }
}

// Adds input fields according to the sender type on plugin Settings page.
function switchSenderType(senderType) {
  switch(senderType) {
        case 'INDIVIDUAL':
              jQuery( '.names1, .names2, .names3, .phone' ).fadeIn(700);
              jQuery( '.edrpou, .up_company_name' ).fadeOut(500);
              break;
        case 'COMPANY':
              jQuery( '.up_company_name, .edrpou, .phone' ).fadeIn(700);
              jQuery( '.names1, .names2, .names3, .up_tin' ).fadeOut(500);
              break;
        case 'PRIVATE_ENTREPRENEUR':
              jQuery( '.up_company_name, .names1, .names2, .names3, .up_tin, .phone' ).fadeIn(700);
              jQuery( '.edrpou' ).fadeOut(500);
              break;
        default:
            jQuery( '.edrpou, .names1, .names2, .names3, .up_company_name, .up_tin, .phone' ).hide();
    }
}
jQuery(document).ready(function() {
    // Adds input fields according to the sender type
    var upSenderType = jQuery( '#up_sender_type' ).val();
    switchSenderType(upSenderType);
    jQuery('#up_sender_type').change(function() {
        upSenderTypeChange = jQuery( '#up_sender_type' ).val();
        switchSenderType(upSenderTypeChange);
    });
}); // ready()
