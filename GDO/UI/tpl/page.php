<?php
namespace GDO\UI\tpl;
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Krombacher Cash-Korken</title>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta http-equiv="language" content="de">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta name="robots" content="index, follow">
    <meta name="description" content="Exklusive Deals, brandheiße Gewinnspiele und Aktionen - das erwartet dich als Krombacher Freund. Außerdem bekommst du 10% Rabatt dauerhaft im Shop. Jetzt anmelden!&nbsp;">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Krombacher">
    <meta property="og:description" content="">
    <meta property="og:title" content="">
    <meta property="og:url" content="https://freunde.krombacher.de/">

    <meta property="og:image" content="/images/Krombacher_Cash-Korken_Aktion_OpenGraph.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta property="twitter:card" content="summary">
    <meta property="twitter:site" content="@krombacher">
    <meta property="twitter:creator" content="@krombacher">
    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="theme-color" content="#ffffff">

    <link href="/apple-touch-icon.png" rel="apple-touch-icon" sizes="180x180">
    <link href="/favicon-32x32.png" rel="icon" type="image/png" sizes="32x32">
    <link href="/favicon-16x16.png" rel="icon" type="image/png" sizes="16x16">

    <script async="" src="https://www.googletagmanager.com/gtm.js?id=GTM-PTF2VXC"></script><script id="usercentrics-cmp" src="https://app.usercentrics.eu/browser-ui/latest/loader.js" data-settings-id="mjB3nOMEw" data-tcf-enabled="" data-avoid-prefetch-services=""></script><script type="module" src="https://app.usercentrics.eu/browser-ui/2.30.1/index.module.js"></script> 
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event' : 'page-visit',
            'registeredID' : '5000126825'
        });
        
    </script>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PTF2VXC');</script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.0.2/glide.js"></script>
    <script>
let Application = {
  getJSON(json){
    try {
      let jso = JSON.parse(json)
      return jso
    } catch(ex) {
      return {}
    }
  },
  getCookie(cname) {
    return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmZWF0dXJlcyI6IjAiLCJpc3MiOiJhY2FyZG8iLCJhY2FyZG8iOiJURVNUIiwiZXhwIjoyMzgyNDg3Mjg5LCJ1c2VyTmFtZSI6ImVyIiwiaWF0IjoxNjQyNDk4NDMzLCJ1c2VyIjoiNTY3IiwiZW1haWwiOiJta29iaW9sa2FAYWNhcmRvLmNvbSJ9.l4BPKo0P3VrhzpGNCvOzF9m07GqHAdJXSuRjRWGiv6A'
    return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmZWF0dXJlcyI6IjAiLCJpc3MiOiJhY2FyZG8iLCJhY2FyZG8iOiJURVNUIiwiZXhwIjoyMzgyNDg3Mjg5LCJ1c2VyTmFtZSI6ImVyIiwiaWF0IjoxNjQyMTYyNTMxLCJ1c2VyIjoiMTIzIiwiZW1haWwiOiJta29iaW9sa2FAYWNhcmRvLmNvbSJ9.Z-zb1hbKoaZ8mhHPh17MlSJ8doPGY9obOTktn-b5dJs'
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  },
  xhrPost(endpoint, body, callbackSuccess, callbackFail, request) {
    if (request === undefined) {
      request = "POST"
    }
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open(request, endpoint, true); // true for asynchronous 
    xmlHttp.setRequestHeader('Content-type', 'application/json');
//    xmlHttp.setRequestHeader('Authorization', this.getCookie("jwt"));
    xmlHttp.onreadystatechange = (function(success, fail) { 
      if (xmlHttp.readyState == 4) {
        if(xmlHttp.status == 200) {
          success(this.getJSON(xmlHttp.responseText));
        } else {
          fail(this.getJSON(xmlHttp.responseText));
        }
      }
    }).bind(this,callbackSuccess,callbackFail)
    xmlHttp.send(typeof body === 'string' ? body : JSON.stringify(body));
  },
  initCodeInput(){
console.log('ici')
    let wrapper = document.querySelector('.code-input-wrapper')
    let inputs = document.querySelectorAll('.code-input input')
    let button = document.querySelector('.input-code-button')
    let responseContainerSuccess = document.querySelector('.input-code-response--success')
    let responseContainerFail = document.querySelector('.input-code-response--fail')
    let buttonAgain = document.querySelectorAll('.input-code-again-button')
    if (wrapper !== null &&
        button !== null &&
        responseContainerSuccess !== null &&
        responseContainerFail !== null &&
        inputs.length == 10 ) {
console.log('ici2')
      for (let ba = 0; ba < buttonAgain.length; ba++) {
        buttonAgain[ba].addEventListener('click',(function(_inputs){
          wrapper.classList.remove('success')
          wrapper.classList.remove('fail')
          for (let i = 0; i < _inputs.length; i++) {
            _inputs[i].value = ''
          }
          _inputs[0].focus()
        }).bind(this,inputs))
      }
      for (let i = 0; i < inputs.length; i++) {
        inputs[i].addEventListener('keyup',(function(_idx, _inputs, e){
          let key = e.key
          let keyCode = e.keyCode
          if (keyCode === 229) {
            if (_inputs[_idx].value.length > 0) {
              _inputs[_idx].value = _inputs[_idx].value[_inputs[_idx].value.length - 1]
            }
            key = _inputs[_idx].value
            keyCode = key.charCodeAt(0)
          }
          e.stopPropagation()
          if (keyCode >= 48 && keyCode <= 57 || keyCode >= 65 && keyCode <= 90 || keyCode >= 97 && keyCode <= 122) {
            _inputs[_idx].value = key.toUpperCase()

            if (_idx + 1 >= _inputs.length) {
//              _inputs[0].focus()
            } else {
              _inputs[_idx + 1].focus()
            }
          } else {
            if (keyCode === 8) {
              _inputs[_idx].value = ''

              if (_idx > 0) {
                _inputs[_idx - 1].focus()
              }
            } else {
              e.preventDefault()
              return false
            }
          }
        }).bind(null,i,inputs))
        inputs[i].addEventListener('focus',(function(_idx, _inputs, e){
          e.stopPropagation()
          let shallJump = true
          for (let i = 0; i <= _idx && i < _inputs.length; i++) {
            if (_inputs[i].value !== "") {
              shallJump = false
            }
          }
          if (_idx > 0 && shallJump) {
            _inputs[0].focus()
          } else {
            e.preventDefault()
            return false            
          }
        }).bind(null,i,inputs))
      }
      button.addEventListener('click',(function(_button,_inputs,_successContainer,_failContainer,e){
        if (_button._sending === true) {
          return
        } else {
          let code = ''
          for (let i = 0; i < _inputs.length; i++) {
            code += _inputs[i].value
          }
          if (code.length !== 10) {
console.error('code not 10 signs')
            _failContainer.innerHTML = 'Bitte gebe den kompletten 10-stelligen Code ein!'
            wrapper.classList.add('fail')
            _button._sending = false
            return
          }
          _button._sending = true
          let _TRANSACTION_ID_ = '5000126825-' + Date.now()
          // reserve code
          this.xhrPost(
            '/prize/reserve',
            {
              transaction_id: _TRANSACTION_ID_,
              code: { id: code }
            },
            (function(jso){ // sucess
console.info('SUCCESS - /prize/reserve')
console.info(jso)
              switch (jso.status) {
                case 100: // code not valid or no prize
                  _failContainer.innerHTML = 'Code ist ungültig oder kein Gewinn'
                  _button._sending = false
                  wrapper.classList.add('fail')
                break

                case 101: // unassign
                  _failContainer.innerHTML = 'Dieser Code wurde bereits eingelöst'
                  _button._sending = false
                  wrapper.classList.add('fail')
                break

                case 422: // incomplete data
                  _failContainer.innerHTML = 'Anfrage nicht gültig, bitte laden Sie die Seite neu und versuchen Sie es nochmal'
                  _button._sending = false
                  wrapper.classList.add('fail')
                break

                case 403: // banned user
                  _failContainer.innerHTML = 'Die Eingabe wurde aufgrund von zu vielen falschen Codes gesperrt. Bitte wende dich an unser Customer Care Team.'
                  _button._sending = false
                  wrapper.classList.add('fail')
                break

                case 200: // reserve successful
                  let bigPrize = false
                  this.xhrPost(
                    '/prize/assign',
                    {
                      transaction_id: _TRANSACTION_ID_, 
                      code: {  
                       id: code
                      }
                    },
                    (function(product, prize, jso){

console.info('SUCCESS - /prize/assign')
console.info(jso)
                      switch (jso.status) {
                        case 200: // assign successful
                          let content = '<br><span class="color-primary font-size--lmd font-weight--700">' + prize + ' €</span><br><br>Herzlichen Glückwunsch zu deinem Gewinn!<br>Dein Gewinncode wurde deinem Cash-Konto gutgeschrieben.'
                          if (prize >= 10000) {
                            let wall = document.querySelector('.wall--attention-10000')
                            if (wall !== null) {
                              wall.classList.remove('done')
                              wall.classList.add('active')
                            }
                            content += '<br><br>Bitte bewahren Sie diesen Kronkorken auf, damit der Gewinn ausgezahlt werden kann.'
                          } else {
                            if (prize >= 100) {
                              let wall = document.querySelector('.wall--attention-100')
                              if (wall !== null) {
                                wall.classList.remove('done')
                                wall.classList.add('active')
                              }
                              content += '<br><br>Bitte bewahren Sie diesen Kronkorken auf, damit der Gewinn ausgezahlt werden kann.'
                            }
                          }
                          _successContainer.innerHTML = content
                          _button._sending = false
                          wrapper.classList.add('success')
                        break

                        default:
                          switch(jso.status) {
                            case 100: // code not valid or no prize
                              _failContainer.innerHTML = 'Code ist kein Gewinn'
                            break

                            case 101: // unassign
                              _failContainer.innerHTML = 'Gewinn ist nicht für diesen Kunden reserviert'
                            break

                            case 422: // incomplete data
                              _failContainer.innerHTML = 'Anfrage nicht gültig, bitte lade Sie die Seite neu und versuchen Sie es nochmal'
                            break

                            default:
                              _failContainer.innerHTML = 'Unerwarteter Fehler'
                          }
                          _button._sending = false
                          wrapper.classList.add('fail')

console.log('call prize release 1')
                          this.xhrPost(
                            '/prize/release',
                            {
                              transaction_id: _TRANSACTION_ID_, 
                              code: {  
                               id: code
                              }
                            },
                            (function(jso){
console.info('code released',jso)
                            }).bind(this),
                            (function(js){
console.error('code not released',jso)
                            }).bind(this)
                          )

                      }


                    }).bind(this,jso.value.sort,+jso.value.prize),
                    (function(jso){
console.error('ERROR - /prize/assign')
console.error(jso)
console.log('call prize release 2')
                      _failContainer.innerHTML = 'Unerwarteter Fehler'
                      _button._sending = false
                      wrapper.classList.add('fail')
                      
                      this.xhrPost(
                        '/prize/release',
                        {
                          transaction_id: _TRANSACTION_ID_, 
                          code: {  
                           id: code
                          }
                        },
                        (function(jso){
console.info('code released - 2',jso)
                        }).bind(this),
                        (function(js){
console.error('code not released - 2',jso)
                        }).bind(this)
                      )

                    }).bind(this)
                  )

                break

                default:
                  _failContainer.innerHTML = 'Unerwarteter Fehler'
                  _button._sending = false
                  wrapper.classList.add('fail')

// unprocessed case
              }
            }).bind(this),
            (function(jso){ // common fail
// common Fail
console.error('ERROR - /prize/reserve')
console.error(jso)
              _failContainer.innerHTML = 'Unerwarteter Fehler'
              _button._sending = false
              wrapper.classList.add('fail')
            }).bind(this)
          )
        }
      }).bind(this,button,inputs,responseContainerSuccess,responseContainerFail))
    }
  },
  initNavigation(){
    console.log('nav')
    let navButton = document.querySelector('.burger-button')
    if (navButton !== null) {
      let navTarget = document.querySelector(navButton.dataset.target + '')
      if (navTarget !== null) {
        navButton.addEventListener('click',(function(button,target,e){
          e.stopPropagation()
          e.preventDefault()
          if (button.classList.contains('animating')) {
            return false
          } else {
            if (button.classList.contains('burger-button--open')) {
              button.classList.add('animating')
              button.classList.add('burger-button--closed')
              target.classList.add('opened')
              setTimeout((function(b){
                b.classList.remove('burger-button--open')
              }).bind(null,button),50)
              setTimeout((function(b){
                b.classList.remove('animating')
              }).bind(null,button),500)
            } else {
              if (button.classList.contains('burger-button--closed')) {
                button.classList.add('animating')
                button.classList.add('burger-button--open')
                target.classList.remove('opened')
                setTimeout((function(b){
                  b.classList.remove('burger-button--closed')
                }).bind(null,button),50)
                setTimeout((function(b){
                  b.classList.remove('animating')
                }).bind(null,button),500)
              } else {
// shall not happen
              }
            }
          }
        }).bind(null,navButton,navTarget))
      }
    }
  },
  _animateScroll(c,toScroll,scrollTimes,timeToScroll){
    let scrollNow = c.scrollTop
    for (let t = 0; t < scrollTimes; t++) {
      setTimeout((function(_c,_v, last){
        _c.scrollTop = _v
      }).bind(null,c,scrollNow + (((1+t) / scrollTimes) * toScroll)),((t + 1) / scrollTimes) * timeToScroll, t+1 >= scrollTimes)
    }
  },
  initScrollTos(){
    let scrollTos = document.querySelectorAll('[data-scroll-to]')
    for (let st = 0; st < scrollTos.length; st++) {
      scrollTos[st].addEventListener('click',(function(trg){
        let target = document.querySelector('[data-scroll-to-target="' + trg + '"]')
        if (target !== null) {
          let bounds = target.getBoundingClientRect()
          this._animateScroll(document.documentElement,bounds.y,200,400)
        }
      }).bind(this,scrollTos[st].dataset.scrollTo))
    }
  },
  initSliders(){
    let sliders = document.querySelectorAll('.slider-section')
    for (let s = 0; s < sliders.length; s++) {

      let hero = sliders[s].querySelector('.glide.hero')

      let glide = new Glide(hero, {
        type: 'carousel',
        animationDuration: 800,
      //  autoplay: 4500,
        focusAt: '1',
        startAt: 1,
        perView: 1, 
      });

      let pages = sliders[s].querySelectorAll('[data-idx]')
      glide.on('run', function(p1,p2,p3) {
        let index = glide.index === 0 ? pages.length : glide.index
        for (let i = 0; i < pages.length; i++) {
          if (index === +pages[i].dataset.idx) {
            pages[i].classList.add('active')
          } else {
            pages[i].classList.remove('active')
          }
        }
      })
      glide.on('build.after', function(){
        for (let i = 0; i < pages.length; i++) {
          pages[i].addEventListener('click',(function(idx){
            glide.go('='+idx)
          }).bind(null,+pages[i].dataset.idx))
        }
      })

      let arrows = sliders[s].querySelectorAll('.slide-arrow')
      for (let a = 0; a < arrows.length; a++) {
        arrows[a].addEventListener('click',function(){
          glide.go(arrows[a].dataset.direction)
        })
      }
      glide.mount();
    }
  },
  initAccordions(){
    let accordions = document.querySelectorAll('.accordion')
    for (let a = 0; a < accordions.length; a++) {
      let entities = accordions[a].querySelectorAll('.accordion-entity')
      for (let e = 0; e < entities.length; e++) {
        let question = entities[e].querySelector('.accordion-question')
        if (question !== null) {
          question.addEventListener('click',(function(allEntities, entity){
            if (!entity.classList.contains('active')) {
              for (let ae = 0; ae < allEntities.length; ae++) {
                allEntities[ae].classList.remove('active')
              }
            }
            entity.classList.toggle('active')
          }).bind(null,entities,entities[e]))
        }
      }
    }
  },
  initVideos(){
    let buttons = document.querySelectorAll('.video-content .play-button[data-iframe]')
    for (let b = 0; b < buttons.length; b++) {
      buttons[b].addEventListener('click',(function(button){
        let parent = button.parentNode
        let children = parent.children
        for (let c = 0; c < children.length; c++) {
          children[c].style.display = 'none'
        }
        let iframe = document.createElement('iframe')
        iframe.setAttribute('frameborder','0')
        iframe.setAttribute('allowfullscreen','1')
        iframe.setAttribute('allow','accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture')
        iframe.setAttribute('title','YouTube video player')
        iframe.setAttribute('width','1200')
        iframe.setAttribute('height','675')
        iframe.setAttribute('src',button.dataset.iframe)
        parent.appendChild(iframe)
      }).bind(null,buttons[b]))
    }
  },
  initWallPayout(){
    let pButton = document.querySelector('.payout-button')
    let codes = [{product: 'PILS', id: 'KPHZXE733H', prize: 1},{product: 'PILS', id: 'ACMTWJHRZ4', prize: 1},{product: 'PILS', id: 'JH4FNH4GNR', prize: 1},{product: 'PILS', id: 'AXYA7PCDGB', prize: 1},{product: 'PILS', id: 'EE9D4YNBAT', prize: 1},{product: 'PILS', id: 'NTHHBPFDGD', prize: 1}]

    let amount = 0
    for (let c = 0; c < codes.length; c++) {
      amount += codes[c].prize
    }
    let wall = document.querySelector('.wall--payout')
    let form = document.querySelector('.wall--payout .payout-form')
    if (wall !== null && form !== null && pButton !== null) {
      wall.addEventListener('click',(function(_wall, e){
        if (e.target === _wall) {
          _wall.classList.remove('active')
        }
      }).bind(null,wall))
      pButton.addEventListener('click',(function(_wall){
        _wall.classList.add('active')
      }).bind(null,wall))

      let fbWall = document.querySelector('.wall--feedback')
      let fbmessage = document.querySelector('.wall--feedback .fb-message')
      let fbOK = document.querySelector('.wall--feedback .button-ok')
      if (fbOK !== null) {
        fbOK.addEventListener('click',function(){
          window.location.reload(true)
        })
      }

      form.addEventListener('submit',(function(e){
        e.preventDefault()
        e.stopPropagation()

        let iban = form.querySelector('input[name="iban"]')
        let adult = form.querySelector('input[name="over18"]')
        let recipient = form.querySelector('input[name="recipient"]')
console.log('subs')
        if (iban !== null && recipient !== null && adult !== null) {
          let _TRANSACTION_ID_ = '5000126825-' + Date.now()

          let body = {
            transaction_id: _TRANSACTION_ID_,
            customer: {
              id: "5000126825",
              iban: iban.value,
              recipient: recipient.value
            },
            adult: adult.checked,
            amount: amount,
            codes: codes,
            address: {
              city: "",
              zipcode: "",
              street: ""
            }
          }

          let street = form.querySelector('input[name="street"]')
          let zipcode = form.querySelector('input[name="zipcode"]')
          let city = form.querySelector('input[name="city"]')

          if (street !== null && zipcode !== null && city !== null) {
            body.address.street = street.value
            body.address.zipcode = zipcode.value
            body.address.city = city.value
          } else {
            delete body.address
          }
          this.xhrPost(
            '/payout',
            body,

            (function(jso){ // sucess

              switch(jso.status) {
                case 100:
                  fbmessage.innerHTML = 'Auszahlung konnte nicht durchgeführt werden, da mindestens ein Code nicht gültig ist oder nicht dem Kunden zugewiesen ist.'
                  fbWall.classList.remove('state--success')
                  fbWall.classList.add('state--error')
                break

                case 105:
                  fbmessage.innerHTML = 'Auszahlungsbetrag entspricht nicht dem Betrag der Codes'
                  fbWall.classList.remove('state--success')
                  fbWall.classList.add('state--error')
                break

                case 421:
                  fbmessage.innerHTML = 'Die eingegebene IBAN ist von keiner deutschen Bank.'
                  fbWall.classList.remove('state--success')
                  fbWall.classList.add('state--error')
                break

                case 422:
                  fbmessage.innerHTML = 'Die eingegebene IBAN ist nicht gültig'
                  fbWall.classList.remove('state--success')
                  fbWall.classList.add('state--error')
                break

                case 200:
                  let wPayoutFeedback = document.querySelector('.wall.wall--payout-feedback')
                  let wPayoutFeedbackButton = document.querySelector('.wall.wall--payout-feedback .button')
                  if (wPayoutFeedback !== null && wPayoutFeedbackButton !== null) {
                    wPayoutFeedback.classList.add('active')
                    wPayoutFeedbackButton.addEventListener('click',function(){
                      window.location.reload(true)
                    })
                  } else {
                    window.location.reload(true)
                  }
                break

                default:
                  fbmessage.innerHTML = 'Die Auszahlung konnte nicht durchgeführt werden. Versuchen Sie es bitte noch ein Mal oder kontaktieren Sie den Support.'
              }


            }.bind(this)),


            (function(jso){ // fail
              if (fbWall !== null && fbOK !== null && fbmessage !== null) {
                fbmessage.innerHTML = 'Die Auszahlung konnte nicht durchgeführt werden. Versuchen Sie es bitte noch ein Mal oder kontaktieren Sie den Support.'                
                fbWall.classList.remove('state--success')
                fbWall.classList.add('state--error')

                switch(jso.status) {
                  case 100:
                    fbmessage.innerHTML = 'Auszahlung konnte nicht durchgeführt werden, da mindestens ein Code nicht gültig ist oder nicht dem Kunden zugewiesen ist.'
                  break

                  case 105:
                    fbmessage.innerHTML = 'Kundendaten ungültig'
                  break

                  default:
                    fbmessage.innerHTML = 'Die Auszahlung konnte nicht durchgeführt werden. Versuchen Sie es bitte noch ein Mal oder kontaktieren Sie den Support.'
                }
              } else {
                window.location.reload()
              }
            }.bind(this)),
            'POST'
          )
        }

        return false
      }).bind(this))

    }
  },
  initWallAttention(){
    console.log('initWallAttention')
    let walls = document.querySelectorAll('.wall--attention')
    console.log(walls)
    for (let w = 0; w < walls.length; w++) {
      let button = walls[w].querySelector('.wall--attention .button')
    console.log(button)
      if (button !== null) {
    console.log('!=null')
        button.addEventListener('click',(function(wall, e){
    console.log('bClick')
          wall.classList.add('done')
          setTimeout(function(){
    console.log('bClick timeout')
            wall.classList.remove('active')
          },500)
        }).bind(null,walls[w]))
      }
    }
  },
  initWallConfirm(){
    let wall = document.querySelector('.wall--confirm')
    let form = document.querySelector('.wall--confirm .confirm-form')
    if (wall !== null && form !== null) {
      wall.classList.add('active')
      form.addEventListener('submit',function(e){
        e.preventDefault()
        e.stopPropagation()

        let tnb = form.querySelector('input[name="tnb"]')
        let bday = form.querySelector('input[name="birthday"]')
        if (tnb !== null && tnb.checked && bday !== null) {
//        if (tnb !== null && tnb.checked) {
          let body = {
            conditions_accepted: true,
            birthdate: bday.value
          }
          Application.xhrPost(
            '/customer/agbs-birthdate',
            body,
            function(jso){

              console.info(wall)
              console.info(jso)
              if (jso.hasOwnProperty('status') && jso.status === 422) {
                wall.classList.add('errors')
// zeige Fehler                
              } else {
                wall.classList.remove('active')
                setTimeout(function(){
                  wall.classList.add('done')
                },500)
              }
            },
            function(jso){
              console.error(jso)
            },
            'PUT'
          )
        }

        return false
      })
    }
  },
  start(){
console.log('phase-2 logged')
    this.initWallAttention()
    this.initWallPayout()
    this.initWallConfirm()
    this.initCodeInput()
    this.initScrollTos()
    this.initVideos()
    this.initSliders()
    this.initAccordions()
    this.initNavigation()
  }
}

function start(){
  Application.start()
}
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.0.2/css/glide.core.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.0.2/css/glide.theme.css">
    <style>
      a {
        color: inherit;
      }
              
@font-face {
  font-family: 'Krombacher Serif';
  font-weight: normal;
  font-style: normal;
  font-display: swap;
  src: url("/fonts/Krombacher-SerifRegular.woff2") format("woff2"), url("/fonts/Krombacher-SerifRegular.woff") format("woff"), url("/fonts/Krombacher-SerifRegular.eot") format("eot");
}

@font-face {
  font-family: 'Krombacher Serif';
  font-weight: normal;
  font-style: italic;
  font-display: swap;
  src: url("/fonts/Krombacher-SerifRegularItalic.woff2") format("woff2"), url("/fonts/Krombacher-SerifRegularItalic.woff") format("woff"), url("/fonts/Krombacher-SerifRegularItalic.eot") format("eot");
}

@font-face {
  font-family: 'Krombacher Serif';
  font-weight: 700;
  font-style: normal;
  font-display: swap;
  src: url("/fonts/Krombacher-SerifDemi.woff2") format("woff2"), url("/fonts/Krombacher-SerifDemi.woff") format("woff"), url("/fonts/Krombacher-SerifDemi.eot") format("eot");
}

@font-face {
  font-family: 'Krombacher Serif';
  font-weight: 700;
  font-style: italic;
  font-display: swap;
  src: url("/fonts/Krombacher-SerifDemiItalic.woff2") format("woff2"), url("/fonts/Krombacher-SerifDemiItalic.woff") format("woff");
}

@font-face {
  font-family: 'Krombacher Sans';
  font-weight: normal;
  font-style: normal;
  font-display: swap;
  src: url("/fonts/Krombacher-SansRegular.woff2") format("woff2"), url("/fonts/Krombacher-SansRegular.woff") format("woff"), url("/fonts/Krombacher-SansRegular.eot") format("eot");
}

@font-face {
  font-family: 'Krombacher Sans';
  font-weight: normal;
  font-style: italic;
  font-display: swap;
  src: url("/fonts/Krombacher-SansRegularItalic.woff2") format("woff2"), url("/fonts/Krombacher-SansRegularItalic.woff") format("woff"), url("/fonts/Krombacher-SansRegularItalic.eot") format("eot");
}

@font-face {
  font-family: 'Krombacher Sans';
  font-weight: 700;
  font-style: normal;
  font-display: swap;
  src: url("/fonts/Krombacher-SansDemi.woff2") format("woff2"), url("/fonts/Krombacher-SansDemi.woff") format("woff"), url("/fonts/Krombacher-SansDemi.eot") format("eot");
}

@font-face {
  font-family: 'Krombacher Sans';
  font-weight: 700;
  font-style: italic;
  font-display: swap;
  src: url("/fonts/Krombacher-SansDemiItalic.woff2") format("woff2"), url("/fonts/Krombacher-SansDemiItalic.woff") format("woff");
}


* {
  line-height: 1.4;
  box-sizing: border-box;
}
html, body {
  min-height: 100vh;
  --space: 2.5rem;
  --color-error: #bb0000;
  --color-primary: #c1a367;
  --color-active: #75633f;
  --color-black: #222;
  --color-grey: #d4d4d4;
  --color-paragraph-dark: #626057;
  --color-paragraph-light: #c1a367;

  font-family: 'Krombacher Sans', sans-serif;
  -ms-text-size-adjust: 100%;
  -webkit-text-size-adjust: 100%;
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-smoothing: antialiased;
}

a.default {
  text-decoration: none;
}
a.default:hover {
  text-decoration: underline;
}
body {
  margin: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: var(--color-black);
  background-image: url('/images/cash-korken_background.jpg');
  background-position: top center;
  background-size: cover;
  background-attachment: fixed;

}

body > .page {
  display: flex;
  flex-direction: column;
  width: 100%;
}

nav {
  background-color: #fff;
  width: 100%;
}

nav .logo-image {
  display: inline-block;
  height: 60px;
  max-width: none;
  padding-top: 16px;
  padding-bottom: 16px;
}

body > .page > .main {
  flex: 1 1 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

section {
  margin: 0;
  padding: var(--space) calc(var(--space) / 5);
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: #fff;
  max-width: 1200px;
  width: 100%;
}

section.no-padding {
  padding: 0;
}

section.no-background {
  background-color: transparent;
}


footer {
  background-color: var(--color-black);
  width: 100%;
}

footer.alternative {
  background-color: #eaeaea;
}

footer.alternative > section {
  padding-bottom: 1rem;
}

footer section {
  background: none;
  max-width: initial;
}

footer .social-row {

}
footer .social-row > div {
  height: 4.5rem;
  width: 4.5rem;
  margin-left: .5rem;
  margin-right: .5rem;
  display: flex;
  justify-content: center;
  align-items: center;
}
footer .social-row a {
  line-height: 1;
  text-decoration: none;
}
footer .social-row a i.icon {
  line-height: 1;
  color: var(--color-primary);
  background-color: var(--color-black);
  font-size: 4rem;
  display: block;
  width: 4rem;
  height: 4rem;
  border-radius: 50%;
  transition: color .4s, width .4s, height .4s, font-size .4s;
}

footer .social-row a:hover i.icon {
  width: 4.5rem;
  height: 4.5rem;
  font-size: 4.5rem;
  color: #d9c7a3;
}
.content {
  width: 100%;
}

.content .full-image {
  position: relative;
  line-height: 0;
}

.content .full-image img {
  width: 100%;
}

.content .full-image-layer {
  color: #fff;
}
.content .full-image-layer.colored {
  background-color: var(--color-primary);
}
.content .full-image-layer.type-2 {
  position: absolute;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
}
.button {
  font-family: "Krombacher Sans", Georgia, serif;
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-smoothing: antialiased;
  font-size: 1.25rem;
  font-weight: 700;
  cursor: pointer;
  display: inline-block;
  height: 3rem;
  line-height: 3rem;
  outline: 0;
  overflow: hidden;
  text-align: center;
  text-decoration: none;
  text-overflow: ellipsis;
  text-transform: none;
  white-space: nowrap;
  border-radius: 2px;
  max-width: 100%;
  min-width: 12.5;
  padding: 0 2.5rem;
  transition: background-color .2s, border-color .2s;
  background-color: var(--color-primary);
  border: 1px solid var(--color-primary);
  color: #fff;
}
.button--highlight {
  background-color: #CC1A1B;
  border-radius: 3rem;
  border-color: #cc1a1b;
}
.button-error {
  background-color: #900;
  border: 1px solid #900;
}
.button--secondary {
  border-color: var(--color-primary);
  background-color: transparent;
  color: var(--color-primary);
}

.button:not(.badge):focus,
.button:not(.badge):hover,
.button--secondary:not(.badge):focus,
.button--secondary:not(.badge):hover {
  background-color: #75633f;
  border-color: var(--color-active);
  color: #fff;
}
.button.button-error:not(.badge):focus,
.button.button-error:not(.badge):hover {
  background-color: #700;
  border-color: #700;
  color: #fff;
}
.button.badge {
  cursor: default;
  color: var(--color-black);
}
.button.badge--success {
  border-color: #090;
  background-color: #afa;
}
.button.badge--fail {
  border-color: #900;
  background-color: #faa;
}
.border-bottom {
  border-bottom: 1px solid var(--color-primary);
}
.heading {
  font-size: 2.5rem;
  text-align: center;
  margin: 0 auto 1rem;
  color: var(--color-black);
}

section h2.heading {
  font-family: 'Krombacher Serif', Georgia, serif;
  font-weight: 700;
}

.video-content {
  position: relative;
  width: 100%;
  height: 0;
  padding-top: 56.25%;
}
.video-content .video-poster,
.video-content  iframe {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
}

.video-content .play-button {
  background-color: #c1a367;
  border: 5px solid transparent;
  border-radius: 50px;
  height: 100px;
  left: 50%;
  margin: -50px 0 0 -50px;
  position: absolute;
  top: 50%;
  transition: transform .2s cubic-bezier(.39,.575,.565,1),color .2s cubic-bezier(.39,.575,.565,1),-webkit-transform .2s cubic-bezier(.39,.575,.565,1);
  width: 100px;
}

.video-content:hover .play-button {
  background-color: #d9c7a3;
}
.video-content .play-button:hover {
  cursor: pointer;
}

.video-content .play-button > .play-icon {
  border-bottom: 20px solid transparent;
  border-left: 30px solid #fff;
  border-top: 20px solid transparent;
  height: 0;
  left: 50%;
  margin: -20px 0 0 -10px;
  position: absolute;
  top: 50%;
  width: 0;
}

footer .content {
  max-width: 900px;
}
footer .footer-contact-box {
  max-width: 440px;
  max-height: 272px;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

footer .footer-contact-box img {
  width: 100%;
  height: 100%;
}

.divider {
  margin: 1.5rem auto;
  border-bottom: 1px solid var(--color-paragraph-light);
}
.color-error {
  color: var(--color-error);
}
.color-primary {
  color: var(--color-primary);
}
.color-active {
  color: var(--color-active);
}
.color-white {
  color: #fff;
}
.color-black {
  color: var(--color-black);
}
.color-paragraph-dark {
  color: var(--color-paragraph-dark);
}
.color-paragraph-light {
  color: var(--color-paragraph-light);
}

@media screen and (min-width: 640px) {

  section:not(.no-padding) .content {
    min-width: 592px;
    width: 75%;
  }

}

section .content h2,
section .content p {
  padding: 0 1rem;
}

@media screen and (min-width: 900px) {
  nav .main-nav {
    padding: 0 2rem;
  }
  nav .logo-image {
    height: 62px;
  }

  body > .page {
    padding: 0 var(--space);
  }

  body > .page > .main {
    padding: calc(var(--space) / 2) 0;
  }

  section {
    margin: calc(var(--space) / 2) 0;
  }
}

@media screen and (min-width: 1280px) {
  nav .main-nav {
    padding: 0;
  }
  nav .logo-image {
    height: 73px;
  }  
}




nav section {
  padding: 0;
  margin: 0;
}
nav .main-nav {
  padding: 0 0 0 .5rem;
  width: 100%;
}

@media only screen and (min-width:900px) {
  nav .main-nav {
    padding: 0 .5rem;
  }
}

nav .main-nav * {
  line-height: 1;
}

nav .main-nav {
  text-align: center;
}
nav .main-nav > div {
  border-bottom: 1px solid #eaeaea;
}

nav .main-nav .nav-content {
  display: flex;
  justify-content: flex-end;
  align-items: center;
}
nav .main-nav .nav-content > div {
  margin-left: 2rem;
  display: none;
}
nav .main-nav .nav-content > div:first-child {
  margin-left: 0;
}
nav .nav-link {
  font-size: 1.125rem;
  color: var(--color-paragraph-dark);
  text-decoration: none;
  cursor: pointer;
}
nav .nav-link--logout {
  font-weight: 700;
}
nav .nav-link:hover {
  color: var(--color-primary);
}
nav .nav-link.active {
  color: var(--color-active);
}
nav.end {
  width: 100%;
  height: 4px;
  max-height: none;
  border: 1px none #000;
  background-image: linear-gradient(165deg, #75633f, #c1a367 5%, #ead79d 43%, #c1a367 71%, #75633f 86%);
}

@media only screen and (min-width:900px) {
  nav:not(.always-mobile) .main-nav .nav-content > div {
    display: block;
  }
}

.burger-button {
  -ms-flex-line-pack: stretch;
  align-content: stretch;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: horizontal;
  -webkit-box-direction: normal;
  -ms-flex-flow: row nowrap;
  flex-flow: row nowrap;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-color: #fff;
  border: 0;
  color: transparent;
  font-size: 0;
  height: 3rem;
  padding: 0;
  position: relative;
  width: 3rem;
  cursor: pointer;
}

@media only screen and (min-width:900px) {
  nav:not(.always-mobile) .burger-button {
    display: none;
  }
}

.burger-button:before {
  background-color: #eaeaea;
  content: "";
  display: block;
  height: 40px;
  left: 0;
  position: absolute;
  top: 4px;
  width: 1px;
}


.burger-button .stripe {
  background-color: #c1a367;
  border-radius: 2px;
  display: block;
  height: 4px;
  left: 50%;
  position: absolute;
  -webkit-transform: translate3d(-50%, 0, 0);
  transform: translate3d(-50%, 0, 0);
  width: 100%;
  -webkit-transition: color .2s cubic-bezier(.39, .575, .565, 1), .2s cubic-bezier(.39, .575, .565, 1);
  transition: color .2s cubic-bezier(.39, .575, .565, 1), .2s cubic-bezier(.39, .575, .565, 1);
}

.burger-button .stripes-wrapper {
  height: 20px;
  position: relative;
  width: 26px;
}

.burger-button .stripe--1 {
  top: 0;
}

.burger-button .stripe--2 {
  opacity: 1;
  top: 50%;
  -webkit-transform: translate3d(-50%, -50%, 0);
  transform: translate3d(-50%, -50%, 0);
  -webkit-transition: opacity .4s ease-out;
  transition: opacity .4s ease-out;
}

.burger-button .stripe--3 {
  top: calc(100% - 4px);
}

.burger-button--closed .stripe {
  background-color: #75633f;
}

.burger-button--closed .stripe--1 {
  -webkit-animation: bar-to-right .4s ease-out normal forwards;
  animation: bar-to-right .4s ease-out normal forwards;
}

@-webkit-keyframes bar-to-right {
  50% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0);
    transform: translate3d(-50%, -50%, 0);
  }
  to {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(45deg);
    transform: translate3d(-50%, -50%, 0) rotate(45deg);
  }
}

@keyframes bar-to-right {
  50% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0);
    transform: translate3d(-50%, -50%, 0);
  }
  to {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(45deg);
    transform: translate3d(-50%, -50%, 0) rotate(45deg);
  }
}

.burger-button--closed .stripe--2 {
  opacity: 0;
}

.burger-button--closed .stripe--3 {
  -webkit-animation: bar-to-left .4s ease-out normal forwards;
  animation: bar-to-left .4s ease-out normal forwards;
}

@-webkit-keyframes bar-to-left {
  50% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0);
    transform: translate3d(-50%, -50%, 0);
  }
  to {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(-45deg);
    transform: translate3d(-50%, -50%, 0) rotate(-45deg);
  }
}

@keyframes bar-to-left {
  50% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0);
    transform: translate3d(-50%, -50%, 0);
  }
  to {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(-45deg);
    transform: translate3d(-50%, -50%, 0) rotate(-45deg);
  }
}

.burger-button--open .stripe--1 {
  -webkit-animation: bar-to-top .4s ease-out forwards;
  animation: bar-to-top .4s ease-out forwards;
}

@-webkit-keyframes bar-to-top {
  0% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(45deg);
    transform: translate3d(-50%, -50%, 0) rotate(45deg);
  }
  50% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(0);
    transform: translate3d(-50%, -50%, 0) rotate(0);
  }
  to {
    top: 0;
    -webkit-transform: translate3d(-50%, 0, 0);
    transform: translate3d(-50%, 0, 0);
  }
}

@keyframes bar-to-top {
  0% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(45deg);
    transform: translate3d(-50%, -50%, 0) rotate(45deg);
  }
  50% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(0);
    transform: translate3d(-50%, -50%, 0) rotate(0);
  }
  to {
    top: 0;
    -webkit-transform: translate3d(-50%, 0, 0);
    transform: translate3d(-50%, 0, 0);
  }
}

.burger-button--open .stripe--2 {
  opacity: 1;
}

.burger-button--open .stripe--3 {
  -webkit-animation: bar-to-bottom .4s ease-out forwards;
  animation: bar-to-bottom .4s ease-out forwards;
}

@-webkit-keyframes bar-to-bottom {
  0% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(-45deg);
    transform: translate3d(-50%, -50%, 0) rotate(-45deg);
  }
  50% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(0);
    transform: translate3d(-50%, -50%, 0) rotate(0);
  }
  to {
    top: calc(100% - 4px);
    -webkit-transform: translate3d(-50%, 0, 0);
    transform: translate3d(-50%, 0, 0);
  }
}

@keyframes bar-to-bottom {
  0% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(-45deg);
    transform: translate3d(-50%, -50%, 0) rotate(-45deg);
  }
  50% {
    top: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0) rotate(0);
    transform: translate3d(-50%, -50%, 0) rotate(0);
  }
  to {
    top: calc(100% - 4px);
    -webkit-transform: translate3d(-50%, 0, 0);
    transform: translate3d(-50%, 0, 0);
  }
}


.mobile-nav {
  background-color: #f4f4f4;
  text-align: center;
  width: 100%;
  max-height: 0;
  overflow: hidden;
  transition: max-height .8s;
}
.mobile-nav.opened {
  max-height: 100vh;
}

.mobile-nav-wrapper {
  -ms-flex-line-pack: stretch;
  align-content: stretch;
  -webkit-box-align: stretch;
  -ms-flex-align: stretch;
  align-items: stretch;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-flow: column nowrap;
  flex-flow: column nowrap;
  -webkit-box-pack: start;
  -ms-flex-pack: start;
  justify-content: flex-start;
}

.mobile-nav-wrapper > .mobile-nav-item {
  font-family: Krombacher Sans,Georgia,serif;
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-smoothing: antialiased;
  font-size: 21px;
  font-weight: 700;
  line-height: 1.4;
  color: var(--color-paragraph-dark);
  padding: 16px 0;
  position: relative;
  white-space: nowrap;
  text-decoration: none;
}
.mobile-nav-wrapper > .mobile-nav-item.active {
  color: var(--color-active);
}
.mobile-nav-wrapper > .mobile-nav-item--logout {

}

.mobile-nav-wrapper > .mobile-nav-item:not(:last-child):after {
  background-color: #d9c7a3;
  bottom: 0;
  content: "";
  display: block;
  height: 1px;
  left: 50%;
  margin: auto;
  position: absolute;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
  width: 88%;
}
@font-face {
  font-family: krombacher-icon-font;
  src: url("/fonts/krombacher-icon-font.eot?63d4a2ef1091bfbb29205b73bc85d8b6#iefix") format("embedded-opentype"), url("/fonts/krombacher-icon-font.woff2?63d4a2ef1091bfbb29205b73bc85d8b6") format("woff2"), url("/fonts/krombacher-icon-font.woff?63d4a2ef1091bfbb29205b73bc85d8b6") format("woff");
}

i[class*=" icon-"]:before,
i[class^=icon-]:before {
  font-family: krombacher-icon-font!important;
  font-style: normal;
  font-weight: 400!important;
  font-variant: normal;
  text-transform: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.icon-accordion-minus:before {
  content: "\f101";
}

.icon-accordion-plus:before {
  content: "\f102";
}

.icon-account-logged-in:before {
  content: "\f103";
}

.icon-account:before {
  content: "\f104";
}

.icon-adjustment:before {
  content: "\f105";
}

.icon-arrow-left-oval:before {
  content: "\f106";
}

.icon-arrow-left:before {
  content: "\f107";
}

.icon-arrow-right-oval:before {
  content: "\f108";
}

.icon-arrow-right:before {
  content: "\f109";
}

.icon-arrow-short-down:before {
  content: "\f10a";
}

.icon-arrow-short-left:before {
  content: "\f10b";
}

.icon-arrow-short-right:before {
  content: "\f10c";
}

.icon-arrow-short-up:before {
  content: "\f10d";
}

.icon-arrow-up-bold:before {
  content: "\f10e";
}

.icon-back:before {
  content: "\f10f";
}

.icon-calendar:before {
  content: "\f110";
}

.icon-check:before {
  content: "\f111";
}

.icon-checkmark:before {
  content: "\f112";
}

.icon-close:before {
  content: "\f113";
}

.icon-corner:before {
  content: "\f114";
}

.icon-cross-circle:before {
  content: "\f115";
}

.icon-cross:before {
  content: "\f116";
}

.icon-customer-return:before {
  content: "\f117";
}

.icon-delete-inverted:before {
  content: "\f118";
}

.icon-delete:before {
  content: "\f119";
}

.icon-download:before {
  content: "\f11a";
}

.icon-drag-n-drop:before {
  content: "\f11b";
}

.icon-exchange-image:before {
  content: "\f11c";
}

.icon-external-link:before {
  content: "\f11d";
}

.icon-eye-closed:before {
  content: "\f11e";
}

.icon-eye-crossed-out:before {
  content: "\f11f";
}

.icon-eye-open:before {
  content: "\f120";
}

.icon-eye-opened:before {
  content: "\f121";
}

.icon-eye:before {
  content: "\f122";
}

.icon-facebook:before {
  content: "\f123";
}

.icon-filter:before {
  content: "\f124";
}

.icon-friends:before {
  content: "\f125";
}

.icon-highlight:before {
  content: "\f126";
}

.icon-hourglass:before {
  content: "\f127";
}

.icon-image-upload:before {
  content: "\f128";
}

.icon-image:before {
  content: "\f129";
}

.icon-information:before {
  content: "\f12a";
}

.icon-instagram:before {
  content: "\f12b";
}

.icon-loyalty-adjustment:before {
  content: "\f12c";
}

.icon-loyalty-newsletter-signup:before {
  content: "\f12d";
}

.icon-loyalty-onlineshop-purchase:before {
  content: "\f12e";
}

.icon-loyalty-profile-address-added:before {
  content: "\f12f";
}

.icon-loyalty-raffle-ticket-purchase:before {
  content: "\f130";
}

.icon-loyalty-receipt-cleared:before {
  content: "\f131";
}

.icon-loyalty-signet:before {
  content: "\f132";
}

.icon-loyalty-survey-participation:before {
  content: "\f133";
}

.icon-loyalty-system-correction:before {
  content: "\f134";
}

.icon-loyalty-user-registered:before {
  content: "\f135";
}

.icon-mail:before {
  content: "\f136";
}

.icon-menu-close:before {
  content: "\f137";
}

.icon-menu:before {
  content: "\f138";
}

.icon-minus:before {
  content: "\f139";
}

.icon-newsletter-singnup:before {
  content: "\f13a";
}

.icon-not-available:before {
  content: "\f13b";
}

.icon-onlineshop-purchase:before {
  content: "\f13c";
}

.icon-pagination-left:before {
  content: "\f13d";
}

.icon-pagination-right:before {
  content: "\f13e";
}

.icon-pinterest:before {
  content: "\f13f";
}

.icon-plus:before {
  content: "\f140";
}

.icon-print:before {
  content: "\f141";
}

.icon-product-delete:before {
  content: "\f142";
}

.icon-profile:before {
  content: "\f143";
}

.icon-receipt-cleared:before {
  content: "\f144";
}

.icon-receipt-fail:before {
  content: "\f145";
}

.icon-receipt-success:before {
  content: "\f146";
}

.icon-receipt-upload:before {
  content: "\f147";
}

.icon-rotate-right:before {
  content: "\f148";
}

.icon-rotate:before {
  content: "\f149";
}

.icon-search-inverted:before {
  content: "\f14a";
}

.icon-search:before {
  content: "\f14b";
}

.icon-service:before {
  content: "\f14c";
}

.icon-share:before {
  content: "\f14d";
}

.icon-shop-purchase:before {
  content: "\f14e";
}

.icon-shop-rotate:before {
  content: "\f14f";
}

.icon-shop-upload:before {
  content: "\f150";
}

.icon-shopping-bag-no-circle:before {
  content: "\f151";
}

.icon-shopping-bag:before {
  content: "\f152";
}

.icon-spinner:before {
  content: "\f153";
}

.icon-spotify:before {
  content: "\f154";
}

.icon-survey-participation:before {
  content: "\f155";
}

.icon-tiktok:before {
  content: "\f156";
}

.icon-to-top:before {
  content: "\f157";
}

.icon-twitter:before {
  content: "\f158";
}

.icon-upload:before {
  content: "\f159";
}

.icon-user-logged-in:before {
  content: "\f15a";
}

.icon-user-registered:before {
  content: "\f15b";
}

.icon-user:before {
  content: "\f15c";
}

.icon-warning:before {
  content: "\f15d";
}

.icon-youtube:before {
  content: "\f15e";
}

.icon-zoom-in-cutted-out:before {
  content: "\f15f";
}

.icon-zoom-in-no-circle:before {
  content: "\f160";
}

.icon-zoom-in:before {
  content: "\f161";
}

.icon-zoom-out-cutted-out:before {
  content: "\f162";
}

.icon-zoom-out-no-circle:before {
  content: "\f163";
}

.icon-zoom-out:before {
  content: "\f164";
}.wall {
  display: none;
  justify-content: center;
  align-items: center;
  background-color: rgba(0,0,0,.75);
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  opacity: 0;
  transition: opacity .4s;
  z-index: 100;
}
.wall .errors {
  display: none;
}
.wall.errors .errors {
  display: block;
}
.wall .wall-block {
  width: 400px;
  max-width: 80%;
  background-color: #fefefe;
  padding: 1rem 1.5rem;
  border: 1px solid #222;
  border-radius: .75rem;
  transform: translateY(100vh);
  transition: transform .4s;
  display: flex;
  flex-direction: column;
  max-height: 90%;
  overflow-y: auto;

}
.wall.active {
  display: flex;
  opacity: 1;
}


.wall.active .wall-block {
  transform: translateY(0);
}

.wall.done {
  display: none;
}

.wall .form-input,
.wall .form-date {
  display: block;
  width: 100%;
  padding: 0 0.5rem;
  font-size: calc(1.25625rem + 0.075vw);
  font-weight: 400;
  line-height: 2.19048;
  color: #777;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid transparent;
  border-bottom-color: var(--color-primary);
  appearance: none;
  border-radius: 2px;
  transition: border-color .4s;
  appearance: none;
  outline: none;
}
.wall .form-input:focus,
.wall .form-date:focus {
  border-color: var(--color-primary);
}
.wall .form-checkbox {
  margin-left: 0;
  margin-right: .5rem;
}

.wall .wall--feedback {
  display: none;
}
.wall .wall--feedback.state--success {
  display: flex;
}
.wall .wall--feedback.state--error {
  display: flex;
}
.wall .wall--feedback.state--success + .hide-on-feedback,
.wall .wall--feedback.state--error + .hide-on-feedback {
  display: none;
}.r-table {
  display: flex;
  flex-direction: column;
  --cell-padding: .25rem;
}

.r-table--header {
  display: none;
  border-bottom: 1px solid var(--color-primary);
}

.r-table--header div {
  flex: 1 1 0;
  padding: var(--cell-padding);
}

.r-table--footer {
  display: flex;
  justify-content: space-between;
  border-top: 1px solid var(--color-primary);
  padding-top: var(--cell-padding);
}

.r-table--footer-label {
  padding: var(--cell-padding);
}

.r-table--footer-value {
  padding: var(--cell-padding);
}

.r-table--content {
  display: flex;
  flex-direction: column;
}

.r-table--row {
  display: flex;
  flex-direction: column;
  background-color: #fcfcfc;
}
.r-table--row:nth-child(even) {
  background-color: #f0f0f0;
}
.r-table--cell {
  flex: 1 1 0;
  display: flex;
  padding: var(--cell-padding);
}

.r-table--cell-mobile-header {
  width: 40%;
}

.r-table--cell-value {
  flex: 1 1 0;
}

@media only screen and (min-width:900px) {
  .r-table--header {
    display: flex;
  }
  .r-table--row {
    flex-direction: row;
  }
  .r-table--cell-mobile-header {
    display: none;
  }
}

section[data-scroll-to-target="insert-code"] {
/*
  overflow: hidden;
*/
}

.code-input-wrapper {
/*
  background-color: yellow;
*/
}
.code-input-wrapper .button-block--default {
/*
  background-color: rgba(128,128,128,.25);
*/
}
.code-input-wrapper .button-block--success {
  display: none;
/*
  background-color: rgba(0,255,0,.25);
*/
}
.code-input-wrapper .button-block--fail {
  display: none;
/*
  background-color: rgba(255,0,0,.25);
*/
}
.code-input-wrapper .input-code-response {
  padding: 0 16%;
/*
  background-color: lime;
*/
}
.code-input-wrapper.fail .button-block--fail,
.code-input-wrapper.success .button-block--success {
  display: block;
}
.code-input-wrapper.fail .button-block--default,
.code-input-wrapper.success .button-block--default {
  display: none;
}

.code-input-wrapper .code-input {
  width: 22rem;
  height: 22rem;
  display: flex;
  flex-direction: column;
  padding: 3rem 1.5rem;
  position: relative;
  overflow: hidden;
}

.code-input-wrapper .code-input:before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  transform: translateX(-50%) translateY(-50%);
  background-image: url(/images/Gewinncodeeingabe.jpg);
  background-size: contain;
  background-position: center;
  background-repeat: no-repeat;
  z-index: 1;
  overflow: clip

}
.code-input-wrapper .code-input > div {
  z-index: 5;
  flex: 1 1 0;
  padding: .75rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.code-input-wrapper .code-input > div > div {
  border: 2px solid var(--color-active);
  width: 80%;
  height: 100%;
  display: flex;
  justify-content: center;
}

.code-input-wrapper .code-input > div:first-child > div,
.code-input-wrapper .code-input > div:last-child > div {
  width: 60%;
}

.code-input-wrapper .code-input > div > div > div {
  height: 100%;
  display: flex;
  background-color: #fff;
  flex: 1 1 0;
}

.code-input-wrapper .code-input > div > div > div > input {
  width: 100%;
  height: 100%;
  padding: 0;
  margin: 0;
  outline: none;
  border: none;
  font-size: 2.25rem;
  font-weight: 700;
  line-height: 1;
  text-align: center;
  color: var(--color-paragraph-dark);
  transition: font-size .4s, color .4s;
}
.code-input-wrapper .code-input > div > div > div > input:focus {
  font-size: 2.75rem;
  color: var(--color-black);
}

.code-input-wrapper.success .code-input {
  border-color: var(--color-primary);
}
.code-input-wrapper.success .code-input > div > div > div > input {
  color: var(--color-primary);
  font-size: 2.75rem;
  pointer-events: none;
}

.code-input-wrapper.fail .code-input {
  border-color: #c00;
}
.code-input-wrapper.fail .code-input > div > div > div > input {
  color: #c00;
  font-size: 2.75rem;
  pointer-events: none;
}


.winner-counter {
  display: flex;
  justify-content: center;
}
.winner-counter > div {
  margin: 0 .25rem;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.winner-counter .digit {
  background-color: var(--color-primary);
  color: #fff;
  width: 3.5rem;
  height: 3.5rem;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 3.5rem;
  overflow: hidden;
  font-weight: 700;
  font-size: 2rem;
}
.winner-counter .label {
  color: var(--color-primary);
  margin-top: .5rem;
  font-size: .875rem;
}
.accordion {
  display: flex;
  flex-direction: column;
}

.accordion .accordion-entity {
  background: #fff;
  width: 100%;
  padding: 0 1rem;
  border-top: 1px solid #afada3;
}

.accordion .accordion-entity:first-child {
  border-top-width: 0;
}

.accordion .accordion-entity .accordion-question {
  position: relative;
  cursor: pointer;
}

.accordion .accordion-entity .accordion-question .title {
  font-size: 1.375rem;
  padding: 1rem 3rem 1rem 0;
  font-weight: 700;
}

.accordion .accordion-entity .accordion-question .status-button {
  position: absolute;
  top: 50%;
  right: 0;
  transform: translateY(-50%);
}

.accordion .accordion-entity .accordion-question .status-button svg {
  fill: var(--color-primary);
  width: 2rem;
  height: 2rem;
  cursor: pointer;
}

.accordion .accordion-entity .accordion-question:hover .status-button svg {
  fill: var(--color-active);
}

.accordion .accordion-entity .accordion-question .status-button svg:first-child {
  display: block;
}

.accordion .accordion-entity .accordion-question .status-button svg:last-child {
  display: none;
}

.accordion .accordion-entity.active .accordion-question .status-button svg:first-child {
  display: none;
}

.accordion .accordion-entity.active .accordion-question .status-button svg:last-child {
  display: block;
}

.accordion .accordion-entity .accordion-answer {
  max-height: 0;
  overflow-y: scroll;
  color: #626057;
  font-size: 1.375rem;
  transition: max-height .4s;
}

.accordion .accordion-entity .accordion-answer p {
  margin: 0 0 1rem;
}

.accordion .accordion-entity .accordion-answer p:last-child {
  margin: 0;
}

.accordion .accordion-entity.active .accordion-answer {
  max-height: 50vh;
  overflow-y: scroll;
}
.bullet-list {
  display: flex;
  flex-direction: column;
}

.bullet-list .bullet {
  width: 100%;
  margin-bottom: .5rem;
}

.bullet-list .bullet .bullet-label {
  font-size: 1.125rem;
  color: var(--color-primary);
  font-weight: 700;
}

.bullet-list .bullet .bullet-content {
  padding: .5rem 1.5rem 1.5rem;
}

.bullet-list .bullet .bullet-content .list-num {
  width: 2rem;
  display: inline-block;
}


section.slider-section {
  overflow: hidden;
}
.slider {
  width: 100%;
  height: 500px;
  position: relative;
  --color-backpanel: #f4f4f4;
}
.slider--content {
  max-width: 400px;
  width: 100%;
}
.slider > div {
  z-index: 5;
}
.slider > div.backpanel {
  z-index: 1;
  position: absolute;
  top: 4rem;
  left: calc((100vw - 100%) / -2);
  width: 100vw;
  height: calc(100% - 4rem);
  background-color: var(--color-backpanel);
}
.glide__slide {
  margin: 0;
  display: flex;
  flex-direction: column;
}
.glide__slide > div {
  width: 100%;
  height: 50%;
  text-align: center;
}
.glide__slide > div.product-image {

}

.glide__slide .product-image {
  background-size: contain;
  position: relative;
}
.glide__slide .product-image img {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translateY(-50%) translateX(-50%);
  height: 100%;
}
.active[data-idx] {
  color: red;
}
.slide-arrow {
  margin: .5rem;
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
color: #fff;
  background-color: var(--color-primary);
  position: relative;
  cursor: pointer;
}

.slide-arrow > svg {
  fill: #fff;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translateX(-50%) translateY(-50%);
  width: 90%;
}
.slide-arrow:hover {
  background-color: var(--color-active);
}

.pages-block {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1rem;
}
.slider-page {
  position: relative;
  margin: 0 .25rem;
  width: .5rem;
  height: .5rem;
  border-radius: 50%;
  cursor: pointer;
  background-color: #eaeaea;
  transition: background-color .2s, border-color .2s, width .2s, height .2s;
}
.slider-page img {
  display: none;
  transition: height .2s;
}
.slider-page:hover {
  background-color: #75633f;
}
.slider-page.active,
.slider-page.active:hover {
  background-color: var(--color-primary);
}


@media screen and (min-width: 640px) {
  .slider {
    height: 70vw;
    margin-bottom: 6rem;
  }
  .slider > div.backpanel {
    bottom: 0;
    top: auto;
    height: 376px;
  }
  .slider--content {
    max-width: initial;
width: 100%;
  }
  .glide__slide {
    flex-direction: row;
  }
  .glide__slide > div {
    height: 100%;
    width: 45%;
    padding-left: 1rem;
    padding-right: 1rem;
    text-align: left;
  }
  .glide__slide > div.product-image {
    width: 55%;
  }
  .glide__slide .product-image img {
    height: auto;
    width: 100%;
    top: 0;
    top: 3rem;
    transform: translateX(-50%);
  }
  .glide__slide > div .border-bottom {
    padding-bottom: 0.5rem;
    margin-bottom: 0.5rem;    
  }
  .slide-arrow {
    margin: 1rem;
  }

  .pages-block {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
    z-index: 50;
    margin-top: 0;
  }

  .slider-page {
    background-color: var(--color-backpanel, #f4f4f4);
    width: 3rem;
    height: 3rem;
    border: .1875rem solid #fff;
    transition: transform .3s;
  }
  .slider-page.active {
    border-color: var(--color-primary);
    background-color: var(--color-backpanel, #f4f4f4);
    width: 5rem;
    height: 5rem;
  }
  .slider-page:hover {
    transform: translateY(-16%);
  }
  .slider-page.active:hover {
    transform: initial;
  }
  .slider-page:hover,
  .slider-page.active:hover {
    background-color: var(--color-backpanel, #f4f4f4);
  }
  .slider-page img {
    display: block;
    width: auto;
    height: 60%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
  }
  .slider-page img {
    height: 80%;
  }
}


@media screen and (min-width: 900px) {
  .slider {
    width: 75%;
    height: 500px;
    left: 12.5%;
  }
  .slider > div.backpanel {
    left: 50%;
    transform: translateX(-50%);
    width: calc(100vw - var(--space) * 2);
  }
  .glide__slide > div,
  .glide__slide > div.product-image {
    width: 50%;
  }
  .glide__slide .product-image img {
    height: auto;
    width: 100%;
    top: 25%;
    transform: translateX(-50%) translateY(-25%);
  }
}

@media screen and (min-width: 1280px) {
  .slider > div.backpanel {
    left: 50%;
    transform: translateX(-50%);
    width: 1200px;
  }
  .glide__slide > div,
  .glide__slide > div.product-image {
    width: 55%;
  }
  .glide__slide > div.product-image {
    width: 45%;
  }
}
.leading-0 {
  line-height: 0;
}
.leading-1 {
  line-height: 1;
}
.font-size--default {
  font-size: 1rem;
}
.font-size--sm {
  font-size: .875rem;
}
.font-size--smd {
  font-size: 1.125rem;
}
.font-size--md {
  font-size: 1.25rem;
}
.font-size--lmd {
  font-size: 1.5rem;
}
.font-size--lg {
  font-size: 1.625rem;
}
.font-size--xl {
  font-size: 2rem;
}
.font-size--xxl {
  font-size: 2.5rem;
}
.font-weight--700 {
  font-weight: 700;
}
.text-left {
  text-align: left;
}
.text-center {
  text-align: center;
}
.text-right {
  text-align: right;
}
.text-justify {
  text-align: justify;
}
.relative {
  position: relative;
}
.overflow-hidden {
  overflow: hidden;
}
.flex {
  display: flex;
}
.flex-1 {
  flex: 1 1 0;
}
.flex-col {
  flex-direction: column;
}
.justify-center {
  justify-content: center;
}
.justify-end {
  justify-content: flex-end;
}
.items-center {
  align-items: center;
}
.flex-wrap {
  flex-wrap: wrap;
}
.w-1\/3 {
  width: calc(100% / 3);
}
.w-1\/2 {
  width: 50%;
}
.w-full {
  width: 100%;
}
.h-full {
  height: 100%;
}
.h-auto {
  height: auto;
}

.p-4 {
  padding: 1rem;
}
.py-4 {
  padding-top: 1rem;
  padding-bottom: 1rem;
}
.pb-4 {
  padding-bottom: 1rem;
}
.px-4 {
  padding-left: 1rem;
  padding-right: 1rem;
}
.m-4 {
  margin: 1rem;
}
.mr-4 {
  margin-right: 1rem;
}
.mb-4 {
  margin-bottom: 1rem;
}
.mt-1 {
  margin-top: .25rem;
}
.mt-2 {
  margin-top: .5rem;
}
.mt-4 {
  margin-top: 1rem;
}
.my-2 {
  margin-top: .5rem;
  margin-bottom: .5rem;
}
.my-3 {
  margin-top: .75rem;
  margin-bottom: .75rem;
}
.my-4 {
  margin-top: 1rem;
  margin-bottom: 1rem;
}
.mt-8 {
  margin-top: 2rem;
}
.mb-8 {
  margin-bottom: 2rem;
}
.my-8 {
  margin-top: 2rem;
  margin-bottom: 2rem;
}
.mx-2 {
  margin-left: .5rem;
  margin-right: .5rem;
}
.mx-4 {
  margin-left: 1rem;
  margin-right: 1rem;
}


@media screen and (min-width: 640px) {
  .md\:flex-row {
    flex-direction: row;
  }
  .md\:w-1\/2 {
    width: 50%;
  }
  .md\:w-1\/3 {
    width: calc(100% / 3);
  }
  .md\:w-1 {
    width: 100%;
  }
}

@media screen and (min-width: 900px) {
  .lg\:text-right {
    text-align: right;
  }
  .lg\:flex-row {
    flex-direction: row;
  }
  .lg\:justify-end {
    justify-content: flex-end;
  }
  .lg\:w-1\/2 {
    width: 50%;
  }
  .lg\:w-1 {
    width: 100%;
  }
}
.caution-sign svg {
  width: 10rem;
  height: 10rem;
  fill: #b00;
}

.q-150 {
  width: 150px;
  height: auto;
}

.d-only {
  display: none;
}
.m-only {
  display: initial;
}

@media screen and (min-width: 640px) {
  .d-only {
    display: initial;
  }
  .m-only {
    display: none;
  }
}    </style>
  </head>