/**
* PHP Email Form Validation - v3.7
*/
(function () {
  "use strict";

  let forms = document.querySelectorAll('.php-email-form');

  forms.forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();

      let thisForm = this;
      let action = thisForm.getAttribute('action');
      
      if (!action) {
        displayError(thisForm, 'The form action property is not set!');
        return;
      }

      thisForm.querySelector('.loading').classList.add('d-block');
      thisForm.querySelector('.error-message').classList.remove('d-block');
      thisForm.querySelector('.sent-message').classList.remove('d-block');

      let formData = new FormData(thisForm);

      fetch(action, {
        method: 'POST',
        body: formData,
        credentials: 'include'
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        thisForm.querySelector('.loading').classList.remove('d-block');
        
        if (data.success) {
          thisForm.querySelector('.sent-message').innerHTML = data.message;
          thisForm.querySelector('.sent-message').classList.add('d-block');
          thisForm.reset();
        } else if (data.redirect) {
          window.location.href = data.redirect;
        } else {
          throw new Error(data.message || 'Form submission failed!');
        }
      })
      .catch((error) => {
        displayError(thisForm, error.message);
      });
    });
  });

  function displayError(thisForm, error) {
    thisForm.querySelector('.loading').classList.remove('d-block');
    thisForm.querySelector('.error-message').innerHTML = error;
    thisForm.querySelector('.error-message').classList.add('d-block');
  }

})();
