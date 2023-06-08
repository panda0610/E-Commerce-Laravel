<footer class="dash-footer">
    <div class="footer-wrapper">
        <div class="py-1">
            <span class="text-muted">{{__('Copyright')}} &copy; {{ (App\Models\Utility::getValByName('footer_text')) ? App\Models\Utility::getValByName('footer_text') :config('app.name', 'WorkGo') }} {{date('Y')}}</span>
        </div>
        <div class="py-1">
            <ul class="list-inline m-0">
                <li class="list-inline-item">
                    <a class="link-secondary" href="javascript:">Joseph William</a>
                </li>
                <li class="list-inline-item">
                    <a class="link-secondary" href="javascript:">About Us</a>
                </li>
                <li class="list-inline-item">
                    <a class="link-secondary" href="javascript:">Blog</a>
                </li>
                <li class="list-inline-item">
                    <a class="link-secondary" href="javascript:">Library</a>
                </li>
            </ul>
        </div>
    </div>
</footer>

{{-- <script src="{{ asset('assets/js/site.core.js')}}"></script>    --}}
<script src="{{asset('custom/js/jquery.min.js')}}"></script>

<script src="{{asset('assets/js/plugins/popper.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/feather.min.js')}}"></script>
<script src="{{asset('assets/js/dash.js')}}"></script>

<!-- Page JS -->
<script src="{{ asset('custom/libs/dropzone/dist/min/dropzone.min.js')}}"></script>
<script src="{{ asset('custom/libs/progressbar.js/dist/progressbar.min.js')}}"></script>

<script src="{{ asset('assets/js/plugins/apexcharts.min.js')}}"></script>

<script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{ asset('custom/libs/select2/dist/js/select2.min.js')}}"></script>
<script src="{{ asset('custom/libs/moment/min/moment.min.js')}}"></script>
<script src="{{ asset('custom/libs/@fancyapps/fancybox/dist/jquery.fancybox.min.js')}}"></script>
<script src="{{ asset('custom/libs/fullcalendar/dist/fullcalendar.min.js')}}"></script>
<script src="{{ asset('custom/libs/flatpickr/dist/flatpickr.min.js')}}"></script>
<script src="{{ asset('custom/libs/quill/dist/quill.min.js')}}"></script>
<script src="{{ asset('custom/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js')}}"></script>
<script src="{{ asset('custom/libs/autosize/dist/autosize.min.js')}}"></script>

<!-- Site JS -->
<script src="{{asset('assets/js/plugins/choices.min.js')}}"></script>
<!-- datatable -->
<script src="{{ asset('assets/js/plugins/simple-datatables.js')}}"></script>
<!-- sweet alert Js -->
<script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>

<script src="{{asset('assets/js/plugins/bootstrap-switch-button.min.js')}}"></script>

<script src="{{ asset('custom/js/letter.avatar.js')}}"></script>

<script type="text/javascript" src="{{ asset('custom/js/custom.js') }}"></script>
@if(Session::has('success'))
    <script>
        show_toastr('{{__('Success')}}', '{!! session('success') !!}', 'success');
    </script>
    {{ Session::forget('success') }}
@endif
@if(Session::has('error'))
    <script>
        show_toastr('{{__('Error')}}', '{!! session('error') !!}', 'error');
    </script>
    {{ Session::forget('error') }}
@endif
@if(App\Models\Utility::getValByName('gdpr_cookie') == 'on')
    <script type="text/javascript">

        var defaults = {
            'messageLocales': {
                /*'en': 'We use cookies to make sure you can have the best experience on our website. If you continue to use this site we assume that you will be happy with it.'*/
                'en': "{{App\Models\Utility::getValByName('cookie_text')}}"
            },
            'buttonLocales': {
                'en': 'Ok'
            },
            'cookieNoticePosition': 'bottom',
            'learnMoreLinkEnabled': false,
            'learnMoreLinkHref': '/cookie-banner-information.html',
            'learnMoreLinkText': {
                'it': 'Saperne di più',
                'en': 'Learn more',
                'de': 'Mehr erfahren',
                'fr': 'En savoir plus'
            },
            'buttonLocales': {
                'en': 'Ok'
            },
            'expiresIn': 30,
            'buttonBgColor': '#d35400',
            'buttonTextColor': '#fff',
            'noticeBgColor': '#000',
            'noticeTextColor': '#fff',
            'linkColor': '#009fdd'
        };
    </script>
    <script src="{{ asset('custom/js/cookie.notice.js') }}"></script>
@endif

<script>

    $(document).ready(function() {
              cust_theme_bg();
              cust_darklayout();
          });



          feather.replace();
          var pctoggle = document.querySelector("#pct-toggler");
          if (pctoggle) {
              pctoggle.addEventListener("click", function() {
                  if (
                      !document.querySelector(".pct-customizer").classList.contains("active")
                  ) {
                      document.querySelector(".pct-customizer").classList.add("active");
                  } else {
                      document.querySelector(".pct-customizer").classList.remove("active");
                  }
              });
          }

          var themescolors = document.querySelectorAll(".themes-color > a");
          for (var h = 0; h < themescolors.length; h++) {
              var c = themescolors[h];

              c.addEventListener("click", function(event) {
                  var targetElement = event.target;
                  if (targetElement.tagName == "SPAN") {
                      targetElement = targetElement.parentNode;
                  }
                  var temp = targetElement.getAttribute("data-value");
                  removeClassByPrefix(document.querySelector("body"), "theme-");
                  document.querySelector("body").classList.add(temp);
              });
          }

          function cust_theme_bg() {
              var custthemebg = document.querySelector("#cust-theme-bg");
              // custthemebg.addEventListener("click", function() {
              if (custthemebg.checked) {
                  document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                  document
                      .querySelector(".dash-header:not(.dash-mob-header)")
                      .classList.add("transprent-bg");
              } else {
                  document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                  document
                      .querySelector(".dash-header:not(.dash-mob-header)")
                      .classList.remove("transprent-bg");
              }
              // });
          }
          var custthemebg = document.querySelector("#cust-theme-bg");
          custthemebg.addEventListener("click", function() {
              if (custthemebg.checked) {
                  document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                  document
                      .querySelector(".dash-header:not(.dash-mob-header)")
                      .classList.add("transprent-bg");
              } else {
                  document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                  document
                      .querySelector(".dash-header:not(.dash-mob-header)")
                      .classList.remove("transprent-bg");
              }
          });


        //   function cust_darklayout() {
        //       var custdarklayout = document.querySelector("#cust-darklayout");
        //       // custdarklayout.addEventListener("click", function() {
        //       if (custdarklayout.checked) {

        //           document
        //               .querySelector("#main-style-link")
        //               .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
        //       } else {

        //           document
        //               .querySelector("#main-style-link")
        //               .setAttribute("href", "{{ asset('assets/css/style.css') }}");
        //       }
        //       // });
        //   }

          var custdarklayout = document.querySelector("#cust-darklayout");
          custdarklayout.addEventListener("click", function() {
              if (custdarklayout.checked) {

                  document
                      .querySelector("#main-style-link")
                      .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
              } else {

                  document
                      .querySelector("#main-style-link")
                      .setAttribute("href", "{{ asset('assets/css/style.css') }}");
              }
          });

          function removeClassByPrefix(node, prefix) {
              for (let i = 0; i < node.classList.length; i++) {
                  let value = node.classList[i];
                  if (value.startsWith(prefix)) {
                      node.classList.remove(value);
                  }
              }
          }

  </script>
@stack('script-page')
