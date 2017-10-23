// REMINDER: arrow functions break jQuery context
(($) => {
  const Settings = {
    terms: [],
    notices: [],
    termTemplate (item) {
      return `
        <li class="difficulty-term" data-term-id="${item.term_id}">
          <h3>${item.name}</h3>
          <a href="#" class="term-delete" data-term-id="${item.term_id}">Delete</a>
        </li>
      `
    },
    noticeTemplate (message, type) {
      return `<div class="${type}"><p>${message}</p></div>`
    },
    renderTerms () {
      let ListParent = $('#difficulty-term-list')
      ListParent.empty()

      $.each(this.terms, function (index) {
        let markup = Settings.termTemplate(this)
        ListParent.append(markup)
      })
    },
    renderNotices () {
      let NoticeParent = $('#term-notices')
      NoticeParent.empty()

      $.each(this.notices, function (index) {
        let markup = Settings.noticeTemplate(this.message, this.type)
        NoticeParent.append(markup)
      })
    },
    set termData (newTerms) {
      this.terms = newTerms
      this.renderTerms()
    },
    set noticeData (notices) {
      this.notices = notices
      this.renderNotices()
    },
    getTerms () {
      let reqUrl = `${window.wpApiSettings.root}diy_meta_plugin/v1/diy_meta_difficulty`

      $.ajax({
        url: reqUrl,
        method: 'GET',
        beforeSend: (xhr) => {
          xhr.setRequestHeader(`X-WP-Nonce`, window.wpApiSettings.nonce)
        }
      })
      .done((response, textStatus, jqXHR) => {
        Settings.termData = response
        console.log(response)
      })
    },
    deleteTerm (termId) {
      let reqUrl = `${window.wpApiSettings.root}diy_meta_plugin/v1/diy_meta_difficulty/${termId}`

      $.ajax({
        url: reqUrl,
        method: 'DELETE',
        beforeSend: (xhr) => {
          xhr.setRequestHeader(`X-WP-Nonce`, window.wpApiSettings.nonce)
        }
      })
      .done((response, textStatus, jqXHR) => {
        this.getTerms()
        this.noticeData = [{
          message: 'Difficulty Level Succesfully Deleted',
          type: 'update'
        }]
      })
      .fail((jqXHR, textStatus, errorThrown) => {
        this.getTerms()
        this.noticeData = [{
          message: 'Difficulty Level Not Deleted, Please Try Again',
          type: 'error'
        }]
      })
    },
    createTerm (termName) {
      let reqUrl = `${window.wpApiSettings.root}diy_meta_plugin/v1/diy_meta_difficulty/`

      $.ajax({
        url: reqUrl,
        method: 'POST',
        beforeSend: (xhr) => {
          xhr.setRequestHeader(`X-WP-Nonce`, window.wpApiSettings.nonce)
        },
        data: {
          diy_meta_new_term: termName
        }
      })
      .done((response, textStatus, jqXHR) => {
        this.getTerms()
        this.noticeData = [{
          message: 'Difficulty Level Succesfully Created',
          type: 'update'
        }]
      })
      .fail((jqXHR, textStatus, errorThrown) => {
        this.noticeData = [{
          message: 'Difficulty Level Not Deleted, Please Try Again',
          type: 'error'
        }]
      })
    },
    init () {
      $('.difficulty-term-list').on('click', '.term-delete', (evt) => {
        evt.preventDefault()

        let termID = evt.currentTarget.getAttribute('data-term-id')
        this.deleteTerm(termID)
      })

      $('.difficulty-term-create').on('click', '#diy_meta_new_term-button', (evt) => {
        evt.preventDefault()
        let NewTermInput = $('#diy_meta_new_term')
        let newTerm = NewTermInput.val()
        NewTermInput.val('')
        this.createTerm(newTerm)
      })

      this.getTerms()
    }
  }

  $(document).ready(() => {
    Settings.init()
    $('.bg-color-picker').wpColorPicker()
  })
})(window.jQuery)
