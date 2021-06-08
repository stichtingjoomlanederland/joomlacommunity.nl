function checkResult (id, token) {
  jQuery.ajax({
    async: true,
    url: 'index.php?option=com_jdidealgateway',
    dataType: 'json',
    cache: false,
    data: 'task=logs.checkstatus&format=json&id=' + id + '&' + token + '=1',
    success: function (response) {
      let msg = {}
      if (response.success) {
        // Render the new status
        jQuery('#paymentResult' + id).html(response.data)

        msg.success = []

        // Add the regular message
        msg.success[0] = response.message

        // Add any enqueued messages if they exist
        if (null !== response.messages) {
          for (index = 0; index < response.messages.message.length; ++index) {
            msg.success[index + 1] = response.messages.message[index]
          }
        }

        Joomla.renderMessages(msg)
      } else if (response.success === false) {
        msg.error = []

        // Add the regular message
        msg.error[0] = response.message

        // Add any enqueued messages if they exist
        if (null !== response.messages) {
          for (index = 0; index < response.messages.message.length; ++index) {
            msg.error[index + 1] = response.messages.message[index]
          }
        }

        Joomla.renderMessages(msg)
      }
    },
    error: function (request, status, error) {
      var msg = {}
      msg.error = []
      msg.error[0] = request.responseText
      Joomla.renderMessages(msg)
    }
  })
}

window.addEventListener('DOMContentLoaded', () => {
  const elements = document.getElementsByClassName('logCopy')

  for (var element of elements) {
    element.onclick = (event) => {
      if (copyToClipboard(event.target.dataset.id)) {
        event.target.innerHTML = Joomla.Text._('COM_ROPAYMENTS_LOG_COPIED', 'Log Copied')
        event.target.classList.add('disabled')
      }
    }
  }
})

function copyToClipboard (id) {
  if (document.queryCommandSupported && document.queryCommandSupported('copy')) {
    const iframe = document.getElementById('log' + id).getElementsByTagName('iframe')[0].contentWindow.document
    const copyText = iframe.getElementById('logContent').innerHTML

    const textarea = document.createElement('textarea')
    textarea.textContent = copyText
    textarea.style.position = 'fixed'
    iframe.body.appendChild(textarea)
    textarea.select()
    try {
      return document.execCommand('copy')
    } catch (ex) {
      console.warn('Copy to clipboard failed.', ex)
      return false
    } finally {
      iframe.body.removeChild(textarea)
    }
  }
}
