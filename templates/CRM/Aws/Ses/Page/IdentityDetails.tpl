{foreach name=identity from=$identity item=value key=property}
  <table class="form-layout-compressed">
    <tr class="{if $smarty.foreach.identity.index % 2 == 0}even{else}odd{/if}">
      <td><strong>{$property}</strong></td>
      <td>
        {if is_array($value)}
          {'<br>'|implode:$value}
        {else}
          {if $value == 1}
            {ts}Yes{/ts}
          {elseif !$value}
            {ts}No{/ts}
          {else}
            {$value}
          {/if}
        {/if}
      </td>
    </tr>
  </table>
  <!-- accordion -->
  {*<div id="ses-identities" class="crm-accordion-wrapper crm-ses-identities-block">
    <div class="crm-accordion-header">
      {$attributeSectionName}
    </div>
    <!-- accordion body -->
    <div class="crm-accordion-body">
      <table class="form-layout-compressed">
          {foreach name=attribute from=$attributeValues item=value key=property}
            {if $attributeSectionName == 'Topics'}
              <tr>
                <td>
                  <div class="crm-accordion-wrapper">
                    <div class="crm-accordion-header">
                      {$property}
                    </div>
                    <div class="crm-accordion-body">
                      <table>
                        <tr>
                          <td>{ts}Display name:{/ts}</td>
                          <td>{$value.TopicAttributes.DisplayName}</td>
                        </tr>
                        <tr>
                          <td>{ts}Subscriptions confirmed:{/ts}</td>
                          <td>{$value.TopicAttributes.SubscriptionsConfirmed}</td>
                        </tr>
                        <tr>
                          <td>{ts}Subscriptions deleted:{/ts}</td>
                          <td>{$value.TopicAttributes.SubscriptionsDeleted}</td>
                        </tr>
                      </table>
                      <div class="crm-accordion-wrapper">
                        <div class="crm-accordion-header">
                          {ts}Subscriptions{/ts}
                        </div>
                        <div class="crm-accordion-body">
                          <table>
                            {foreach from=$value.Subscriptions item=subscription key=subId}
                              <tr>
                                <td>{ts}Subscription Arn:{/ts}</td>
                                <td>{$subscription.SubscriptionArn}</td>
                              </tr>
                              <tr>
                                <td>{ts}Endpoint:{/ts}</td>
                                <td>{$subscription.Endpoint}</td>
                              </tr>
                              <tr>
                                <td>{ts}Protocol:{/ts}</td>
                                <td>{$subscription.Protocol}<hr></td>
                              </tr>
                            {/foreach}
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            {else}
              <tr class="{if $smarty.foreach.attribute.index % 2 == 0}even{else}odd{/if}">
                <td><strong>{$property}</strong></td>
                <td>
                  {if is_array($value)}
                    {'<br>'|implode:$value}
                  {else}
                    {if $value == 1}
                      {ts}Yes{/ts}
                    {elseif !$value}
                      {ts}No{/ts}
                    {else}
                      {$value}
                    {/if}
                  {/if}
                </td>
              </tr>
            {/if}
          {/foreach}
      </table>
    </div>
    <!-- /accordion body -->
  </div>*}
  <!-- /accordion -->
{/foreach}

