const Numeral = require('numeraljs')

    $('#diy-project-meta-cost').on('input', function(evt) {
      console.log('input')
      let Input = $(this)
      let string = Numeral(Input.val()).format('$0,0.00')
      Input.val(string)
    })