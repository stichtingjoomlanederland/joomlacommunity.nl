
EasySocial
.require()
.script('site/groups/item')
.done(function($){
    $('[data-es-group-item]').implement(EasySocial.Controller.Groups.Item);
});