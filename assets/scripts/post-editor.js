(($) => {
  $.isNumeric = function (str) {
    return /^[0-9.,]/.test(str)
  }

  $.fn.maskInput = function () {
    let value = $(this).val()
    let stringArray = value.split('')
    let cleanedArray = []
    console.log(stringArray)
    stringArray.forEach((char) => {
      console.log(char)
      if ($.isNumeric(char)) {
        cleanedArray.push(char)
      }
    })
    let clean = cleanedArray.join('')
    $(this).val(clean)
  }

  $(document).ready(() => {
    $('#diy-project-meta-cost').on('input', function (evt) {
      $(this).maskInput()
    })
  })
})(jQuery)
