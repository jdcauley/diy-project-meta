(($) => {
  $.isNumeric = function (str) {
    return /^[0-9.,]+$/.test(str)
  }
  $.fn.maskInput = function () {
    let value = $(this).val()
    if (!$.isNumeric(value)) {
      value = value.substring(0, value.length - 1)
    }
    $(this).val(value)
  }

  $(document).ready(() => {
    $('#diy-project-meta-cost').on('input', function (evt) {
      console.log('input')
      $(this).maskInput()
    })
  })
})(jQuery)
