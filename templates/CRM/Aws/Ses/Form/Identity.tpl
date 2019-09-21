{include file="CRM/Core/Form/EntityForm.tpl"}

{literal}
  <script>
    CRM.$(function($) {

      function toggleFields(fields, display) {
        fields.map(function(fieldName) {
          $('.crm-ses-form-block-' + fieldName).toggle(display);
        });
      }

      var topicFields = ['TopicName', 'TopicDisplayName', 'NotificationTypes'];
      toggleFields(topicFields, false);

      $('#CreateTopic').on('change', function() {
        toggleFields(topicFields, parseInt($(this).val()));
      });

    });
  </script>
{/literal}

