{capture assign=newIdentityURL}
  {crmURL p="civicrm/aws-ses/identity/add" q="action=add&reset=1"}
{/capture}

<div id="ses-identities" class="crm-accordion-wrapper crm-ses-identities-block">
  <div class="crm-accordion-header">
    {ts}Verified Identities{/ts} ({$identitiesCount})
  </div>
  <!-- accordion body -->
  <div class="crm-accordion-body">
    <!-- help message -->
    <div class="help">
      <p>
        {capture assign="linkNewIdentity"}class="action-item crm-popup" href="{$newIdentityURL}"{/capture}
        {ts 1=$linkNewIdentity}
          Click <a %1>Verify new Identity</a> to verify a new domain or email address for SES (Simple Email Service).
        {/ts}
      </p>
    </div>
    <!-- /help message -->
    <!-- action buttons -->
    <div class="action-link">
      <!-- new identity -->
      <a accesskey="N" href="{$newIdentityURL}" class="button crm-popup">
        <span>
          <i class="crm-i fa-plus-circle"></i> {ts}Verify new Identity{/ts}
        </span>
      </a>
      <!-- /new identity -->
      <!-- refresh -->
      <a accesskey="R" href="#" class="button" onClick="refreshTable('.crm-ses-identities-table')">
        <span>
          <i class="crm-i fa-sync"></i> {ts}Refresh{/ts}
        </span>
      </a>
      <!-- /refresh -->
    </div>
    <!-- /action buttons -->
    <!-- results -->
    {if $identitiesCount}
      <table
        class="crm-ses-identities-table crm-ajax-table table-responsive"
        style="width: 100%;"
        data-ajax="{crmURL p='civicrm/aws-ses/identities/ajax/get'}">
        <thead>
          <tr>
            {foreach from=$tableHeaders item=title key=name}
              <th
                data-data="{$name}"
                class="crm-ses-identity-{$name} no-sort">
                {ts}{$title}{/ts}
              </th>
            {/foreach}
          </tr>
        </thead>
      </table>
    <!-- /results -->
    {else}
    <!-- no result message -->
      <div class="messages status no-popup">
        <table class="form-layout">
          <tr>
            <div class="icon inform-icon"></div>
            {ts}No SES identities found.{/ts}
          </tr>
        </table>
      </div>
    {/if}
    <!-- /no result message -->
  </div>
  <!-- /accordion body -->
</div>
<script type="text/javascript">
  {literal}
    function refreshTable(selector) {
      CRM.$(selector).DataTable().ajax.reload();
    }
    CRM.$('body').on('crmPopupFormSuccess, crmPopupClose', function() {
      refreshTable('.crm-ses-identities-table');
    });
  {/literal}
</script>
