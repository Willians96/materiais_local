  // Função para permitir apenas números inteiros
  function allowOnlyNumbers(event) {
    let value = event.target.value;
    value = value.replace(/\D/g, ''); // Remove tudo que não for dígito
    event.target.value = value;
  }

  // Aplica a todos com a classe 'number'
  document.querySelectorAll('.number').forEach(input => {
    input.addEventListener('input', allowOnlyNumbers);
  });

  // Função para permitir números com um único ponto decimal
  function allowDecimal(event) {
    let value = event.target.value;

    // Remove tudo que não for número ou ponto
    value = value.replace(/[^0-9.]/g, '');

    // Garante que só exista um ponto decimal
    const parts = value.split('.');
    if (parts.length > 2) {
      value = parts[0] + '.' + parts.slice(1).join('');
    }

    event.target.value = value;
  }

  // Aplica a todos com a classe 'decimal'
  document.querySelectorAll('.decimal').forEach(input => {
    input.addEventListener('input', allowDecimal);
  });

