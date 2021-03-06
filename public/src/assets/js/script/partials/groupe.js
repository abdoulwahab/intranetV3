// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/public/src/assets/js/script/partials/groupe.js
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 25/11/2019 10:18

$(document).on('change', '.typegroupe_defaut', function (e) {
  $.ajax({
    url: Routing.generate('administration_type_groupe_defaut'),
    method: 'POST',
    data: {
      typegroupe: $(this).val()
    },
    success: function (data) {
      addCallout('Type de groupe par défaut enregistré', 'success')
    }, error: function (e) {
      addCallout('Erreur lors de l\'enregistrement du type de groupe par défaut', 'danger')
    }
  })
})

$(document).on('click', '.add_type_groupe', function () {
  const $semestre = $(this).data('semestre')
  $.ajax({
    url: Routing.generate('administration_type_groupe_new', {semestre: $semestre}),
    method: 'POST',
    data: {
      libelle: $('#type_groupe_libelle_' + $semestre).val(),
      type: $('#type_groupe_type_' + $semestre).val(),
      defaut: $('#type_groupe_defaut_' + $semestre).prop('checked')
    },
    success: function (data) {
      $('#typgeGroupe_bloc_' + $semestre).empty().load(Routing.generate('administration_type_groupe_refresh', {semestre: $semestre}))
      //todo: le refresh ne fait pas la bonne liste?
      addCallout('Type de groupe ajouté', 'success')
    }, error: function (e) {
      addCallout('Erreur lors de l\'ajout du type de groupe', 'danger')
    }
  })
})

$(document).on('click', '.add_groupe', function () {
  const $parent = $(this).data('parent')

  $.ajax({
    url: Routing.generate('administration_groupe_new'),
    method: 'POST',
    data: {
      //todo: ca bug pour le refresh...
      libelle: $('#groupe_libelle_' + $parent).val(),
      ordre: $('#groupe_ordre_' + $parent).val(),
      code: $('#groupe_code_apogee_' + $parent).val(),
      type: $('#groupe_type_groupe_' + $parent).val(),
      parcours: $('#groupe_parcours_' + $parent).val(),
      parent: $parent
    },
    success: function (data) {
      $('#groupe_bloc').empty().load(Routing.generate('administration_groupe_refresh', {parent: $parent}))
      addCallout('Groupe ajouté', 'success')
    }, error: function (e) {
      addCallout('Erreur lors de l\'ajout du groupe', 'danger')
    }
  })
})
