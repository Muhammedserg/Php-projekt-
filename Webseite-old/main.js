document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('contact-form');
  const inputs = form.querySelectorAll('.main-input');
  const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/;
  const numericRegex = /^\d+$/;

  function showAutoCloseAlert(message) {
    alert(message);
    setTimeout(() => {
      let alertBox = document.querySelector('.alert');
      if (alertBox) {
        alertBox.style.display = 'none';
      }

    }, 1000); 
  }

  inputs[0].focus();

  inputs.forEach(input => {
      input.addEventListener('focusout', function() {
          validateInput(input);
      });
  });

  function validateInput(input) {
    const value = input.value.trim();
    if (input.name === 'mail') {
      if (!emailRegex.test(value)) {
        input.classList.add('invalid');
        input.style.borderColor = 'red';
        showAutoCloseAlert('Bitte geben Sie eine gültige E-Mail-Adresse ein. (z.B. name@example.com)');
      } else {
        input.classList.remove('invalid');
        input.style.borderColor = 'green';
      }
    } else if (input.name === 'address') {
      if (value.length <= 10) {
        input.classList.add('invalid');
        input.style.borderColor = 'red';
        showAutoCloseAlert('Die Adresse muss mehr als 10 Zeichen enthalten.');
      } else {
        input.classList.remove('invalid');
        input.style.borderColor = 'green';
      }
    } else if (input.name === 'plz') {
      if (!numericRegex.test(value) || value.length !== 5) {
        input.classList.add('invalid');
        input.style.borderColor = 'red';
        showAutoCloseAlert('Die PLZ muss genau 5 Ziffern enthalten.');
      } else {
        input.classList.remove('invalid');
        input.style.borderColor = 'green';
      }
    } else if (input.name === 'phone') {
      if (!numericRegex.test(value)) {
        input.classList.add('invalid');
        input.style.borderColor = 'red';
        showAutoCloseAlert('Die Telefonnummer darf nur Zahlen enthalten.');
      } else {
        input.classList.remove('invalid');
        input.style.borderColor = 'green';
      }
    } else {
      if (value.length < 3) {
        input.classList.add('invalid');
        input.style.borderColor = 'red';
        showAutoCloseAlert('Mindestens 3 Zeichen erforderlich.');
      } else {
        input.classList.remove('invalid');
        input.style.borderColor = 'green';
      }
    }
  }

  form.addEventListener('submit', function(e) {
    inputs.forEach(input => {
      validateInput(input);
    });

    if (!form.querySelector('.main-input.invalid')) {
      showAutoCloseAlert('Ihre Daten wurden erfolgreich übermittelt.');
    } else {
      e.preventDefault();
      showAutoCloseAlert('Bitte überprüfen Sie die Eingabe in den Feldern.');
    }
  });
});
