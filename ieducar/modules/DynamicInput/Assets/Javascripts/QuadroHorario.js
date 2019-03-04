(function($){
    $(document).ready(function(){
  
        // serie search expect an id for escola
        var $instituicaoField = getElementFor('instituicao');

        var $escolaField = getElementFor('escola');

        var $cursoField = getElementFor('curso');
        var $serieField = getElementFor('serie');
        var $turmaField = getElementFor('turma');
        var $componenteField = getElementFor('componente_curricular');
        var $quadroHorarioField = getElementFor('quadro_horario_horarios');

        var handleGetQuadroHorario = function(resources) {
            var selectOptions = jsonResourcesToSelectOptions(resources['options']);
            updateSelect($quadroHorarioField, selectOptions, "Selecione uma aula");
        }

        var updateQuadroHorario = function(){
            resetSelect($quadroHorarioField);
  
            if ($instituicaoField.val() && $escolaField.val() && $serieField.val() && $turmaField.val() && $componenteField.val() && $componenteField.is(':enabled')) {
                $quadroHorarioField.children().first().html('Aguarde carregando...');
                
                var urlForGetQuadroHorario = getResourceUrlBuilder.buildUrl('/module/DynamicInput/quadroHorario', 'quadroHorarios', {
                    instituicao_id   : $instituicaoField.val(),
                    escola_id        : $escolaField.val(),
                    curso_id         : $cursoField.val(),
                    turma_id         : $turmaField.val(),
                    componente_curricular  : $componenteField.val()
                });

                var options = {
                    url : urlForGetQuadroHorario,
                    dataType : 'json',
                    success  : handleGetQuadroHorario
                };

                getResources(options);
            }
  
            $quadroHorarioField.change();   
        };

        // bind onchange event
        $componenteField.change(updateQuadroHorario);

    }); // ready
})(jQuery);