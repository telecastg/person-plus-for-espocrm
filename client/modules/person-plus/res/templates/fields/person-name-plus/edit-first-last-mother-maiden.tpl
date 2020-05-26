<div class="row">
    <div class="col-sm-3 col-xs-3">
        <select data-name="salutation{{ucName}}" class="form-control">
            {{options salutationOptions salutationValue field='salutationName' scope=scope}}
        </select>
    </div>
    <div class="col-sm-6 col-xs-6">
        <input type="text" class="form-control" data-name="first{{ucName}}" value="{{firstValue}}" placeholder="{{translate 'First Name'}}"{{#if firstMaxLength}} maxlength="{{firstMaxLength}}"{{/if}} autocomplete="espo-first{{ucName}}">
    </div>
    <div class="col-sm-6 col-xs-6">
        <input type="text" class="form-control" data-name="last{{ucName}}" value="{{lastValue}}" placeholder="{{translate 'Last Name'}}"{{#if lastMaxLength}} maxlength="{{lastMaxLength}}"{{/if}} autocomplete="espo-last{{ucName}}">
    </div>
    <div class="col-sm-6 col-xs-6">
        <input type="text" class="form-control" data-name="motherMaiden{{ucName}}" value="{{motherMaidenValue}}" placeholder="{{translate 'Mother Maiden Name'}}"{{#if motherMaidenMaxLength}} maxlength="{{motherMaidenMaxLength}}"{{/if}} autocomplete="espo-MotherMaiden{{ucName}}">
    </div>
</div>
