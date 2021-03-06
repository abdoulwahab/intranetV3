/*
 * Copyright (C) 8 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
 * @file /Users/davidannebicque/htdocs/intranetv3/public/src/assets/js/script/partials/previsionnel.js
 * @author     David Annebicque
 * @project intranetv3
 * @date 02/08/2019 12:40
 * @lastUpdate 02/08/2019 12:39
 */

let nbLignePrevisionnel = 1

//todo: désactivé car off-line. Ajouter les CDN sur base.html.twig
//todo: pourquoi ajaxcomplete?
$(document).ajaxComplete(function () {
  $('.editPrevi').editable({
    type: 'text',
    url: Routing.generate('administration_previsionnel_edit')
    //todo: si success recalculer toute la ligne.
  })
})

$(document).on('change', '#previSemestre', function (e) {
  e.preventDefault()
  e.stopPropagation()
  $('#blocPrevisionnel').empty().load(Routing.generate('administration_previsionnel_semestre', {
    semestre: $(this).val(),
    annee: $(this).data('annee')
  }))
})

$(document).on('click', '#reloadPreviSemestre', function (e) {
  e.preventDefault()
  e.stopPropagation()
  $('#blocPrevisionnel').empty().load(Routing.generate('administration_previsionnel_semestre', {
    semestre: $(this).data('semestre'),
    annee: $(this).data('annee')
  }))
})

$(document).on('change', '#previMatiere', function (e) {
  e.preventDefault()
  e.stopPropagation()
  $('#blocPrevisionnel').empty().load(Routing.generate('administration_previsionnel_matiere', {
    matiere: $(this).val(),
    annee: $(this).data('annee')
  }))
})

$(document).on('click', '#reloadPreviMatiere', function (e) {
  e.preventDefault()
  e.stopPropagation()
  $('#blocPrevisionnel').empty().load(Routing.generate('administration_previsionnel_matiere', {
    matiere: $(this).data('matiere'),
    annee: $(this).data('annee')
  }))
})

$(document).on('change', '#previPersonnel', function (e) {
  e.preventDefault()
  e.stopPropagation()
  $('#blocPrevisionnel').empty().load(Routing.generate('administration_previsionnel_personnel', {
    personnel: $(this).val(),
    annee: $(this).data('annee')
  }))
})

$(document).on('click', '#reloadPreviPersonnel', function (e) {
  e.preventDefault()
  e.stopPropagation()
  $('#blocPrevisionnel').empty().load(Routing.generate('administration_previsionnel_personnel', {
    personnel: $(this).data('personnel'),
    annee: $(this).data('annee')
  }))
})

$(document).on('click', '.previsionnel_add_change', function (e) {
  e.preventDefault()
  e.stopPropagation()
  $('.previsionnel_add_change').removeClass('active show')
  $(this).addClass('active show')
  $('#mainContent').empty().load($(this).attr('href'))
})

$(document).on('click', '#addIntervenantPrevisionnel', function (e) {
  e.preventDefault()
  e.stopPropagation()
  nbLignePrevisionnel++


  $.ajax({
    url: Routing.generate('api_enseignants_departement'),
    method: 'POST',
    success: function (data) {
      let html = '<tr>\n' +
        '                        <td>\n' +
        '                            <select class="form-control" data-live-search="true" name="intervenant_' + nbLignePrevisionnel + '" id="intervenant_' + nbLignePrevisionnel + '">\n' +
        '                                <option value="">Choisir l\'intervenant</option>\n'
      jQuery.each(data, function (index, pers) {
        html = html + '<option value="' + pers.id + '">' + pers.display + '</option>\n'
      })
      html = html + '                            </select>\n' +
        '                        </td>\n' +
        '                        <td><input type="text" name="cm_' + nbLignePrevisionnel + '" id="cm_' + nbLignePrevisionnel + '" data-ligne="' + nbLignePrevisionnel + '" class="form-control chgcm" value="0"></td>\n' +
        '                        <td><input type="number" name="gr_cm_' + nbLignePrevisionnel + '" id="gr_cm_' + nbLignePrevisionnel + '" value="0" data-ligne="' + nbLignePrevisionnel + '" class="form-control chgcm">\n' +
        '                        </td>\n' +
        '                        <td id="ind_cm_' + nbLignePrevisionnel + '">0</td>\n' +
        '                        <td style="background-color: #68C39F"><input type="text" value="0" name="td_' + nbLignePrevisionnel + '" id="td_' + nbLignePrevisionnel + '" data-ligne="' + nbLignePrevisionnel + '"\n' +
        '                                                                     class="form-control chgtd"></td>\n' +
        '                        <td style="background-color: #68C39F"><input type="number" value="0" name="gr_td_' + nbLignePrevisionnel + '" id="gr_td_' + nbLignePrevisionnel + '"\n' +
        '                                                                     data-ligne="' + nbLignePrevisionnel + '" class="form-control chgtd"></td>\n' +
        '                        <td style="background-color: #68C39F" id="ind_td_' + nbLignePrevisionnel + '">0</td>\n' +
        '                        <td style="background-color: #FFC052"><input type="text" value="0" name="tp_' + nbLignePrevisionnel + '" id="tp_' + nbLignePrevisionnel + '" data-ligne="' + nbLignePrevisionnel + '"\n' +
        '                                                                     class="form-control chgtp"></td>\n' +
        '                        <td style="background-color: #FFC052"><input type="number" value="0" name="gr_tp_' + nbLignePrevisionnel + '" id="gr_tp_' + nbLignePrevisionnel + '"\n' +
        '                                                                     data-ligne="' + nbLignePrevisionnel + '" class="form-control chgtp"></td>\n' +
        '                        <td style="background-color: #FFC052" id="ind_tp_' + nbLignePrevisionnel + '">0</td>\n' +
        '                    </tr>'

      $('#nbLigne').val(nbLignePrevisionnel)
      $('#ligneAdd').before(html)
      $('#intervenant_' + nbLignePrevisionnel).selectpicker()
    }
  })


})

$(document).on('change', '.chgcm', function (e) {
  const ligne = $(this).data('ligne')
  const nbSeance = parseFloat($('#cm_' + ligne).val()) / 1.5
  $('#ind_cm_' + ligne).html(nbSeance.toFixed(2))

  updateSynthesePrevisionnel()
})

$(document).on('change', '.chgtd', function (e) {
  const ligne = $(this).data('ligne')
  const nbSeance = parseFloat($('#td_' + ligne).val()) / 1.5
  $('#ind_td_' + ligne).html(nbSeance.toFixed(2))

  updateSynthesePrevisionnel()
})

$(document).on('change', '.chgtp', function (e) {
  const ligne = $(this).data('ligne')
  const nbSeance = parseFloat($('#tp_' + ligne).val()) / 1.5
  $('#ind_tp_' + ligne).html(nbSeance.toFixed(2))

  updateSynthesePrevisionnel()
})

function updateSynthesePrevisionnel () {
  let totalCm = 0
  let totalTd = 0
  let totalTp = 0
  let totalEqTd = 0
  let totalEtu = 0

  let totalHeuresCm = 0
  let totalHeuresTd = 0
  let totalHeuresTp = 0
  //let totalMatiere = 0

  for (let i = 1; i <= nbLignePrevisionnel; i++) {
    let $cm = $('#cm_' + i)
    let $td = $('#td_' + i)
    let $tp = $('#tp_' + i)

    totalCm = totalCm + parseFloat($cm.val()) * parseInt($('#gr_cm_' + i).val())
    totalTd = totalTd + parseFloat($td.val()) * parseInt($('#gr_td_' + i).val())
    totalTp = totalTp + parseFloat($tp.val()) * parseInt($('#gr_tp_' + i).val())

    totalHeuresCm = totalHeuresCm + parseFloat($cm.val())
    totalHeuresTd = totalHeuresTd + parseFloat($td.val())
    totalHeuresTp = totalHeuresTp + parseFloat($tp.val())
    //totalEqTd = totalEqTd + totalCm + totalTd + totalTp;

    totalEtu = totalEtu + parseFloat($cm.val()) + parseFloat($td.val()) + parseFloat($tp.val())
  }
  totalEqTd = totalCm + totalTd + totalTp

  $('#totalCm').html(totalCm.toFixed(2))
  $('#totalTd').html(totalTd.toFixed(2))
  $('#totalTp').html(totalTp.toFixed(2))
  $('#totalEqTd').html(totalEqTd.toFixed(2))
  $('#totalEtu').html(totalEtu.toFixed(2))

  /** vérificiation */
  /* todo: ne fonctionne par, car doit prendre en compte le nombre de groupe et les possibles autres données déjà saisies. */
  $('#totalHeuresCm').html(totalHeuresCm.toFixed(2))
  $('#totalHeuresTd').html(totalHeuresTd.toFixed(2))
  $('#totalHeuresTp').html(totalHeuresTp.toFixed(2))

  let diffCm = parseFloat($('#cmMaquette').html()) - totalHeuresCm
  let diffTd = parseFloat($('#tdMaquette').html()) - totalHeuresTd
  let diffTp = parseFloat($('#tpMaquette').html()) - totalHeuresTp
  $('#diffCm').html(diffCm.toFixed(2))
  $('#diffTd').html(diffTd.toFixed(2))
  $('#diffTp').html(diffTp.toFixed(2))

  if (diffCm < 0) {
    $('#diffCm').addClass('erreurPrevi').removeClass('validePrevi')
  } else {
    $('#diffCm').addClass('validePrevi').removeClass('erreurPrevi')
  }

  if (diffTd < 0) {
    $('#diffTd').addClass('erreurPrevi').removeClass('validePrevi')
  } else {
    $('#diffTd').addClass('validePrevi').removeClass('erreurPrevi')
  }

  if (diffTp < 0) {
    $('#diffTp').addClass('erreurPrevi').removeClass('validePrevi')
  } else {
    $('#diffTp').addClass('validePrevi').removeClass('erreurPrevi')
  }

  //$('#totalMatiere').html(totalMatiere.toFixed(2));
}

/*
$(document).on('change', '#previsionnel_semestre', function () {
  const selectMatiere = $('#previsionnel_matiere')
  if ($(this).val() === '') {
    selectMatiere.empty()
    selectMatiere.append($('<option></option>')
      .attr('value', '')
      .text('Choisir d\'abord un semestre'))
  } else {
    $.ajax({
      url: Routing.generate('api_matieres_semestre', {'semestre': $(this).val()}),
      success: function (data) {

        selectMatiere.empty()
        selectMatiere.append($('<option></option>')
          .attr('value', '')
          .text('Choisir une matière'))
        jQuery.each(data, function (index, matiere) {

          selectMatiere.append($('<option></option>')
            .attr('value', matiere.id)
            .text(matiere.libelle))
        })
      }
    })
  }
})*/

$(document).on('change', '#previsionnel_matiere', function () {
  const volumeMatiere = $('#volumeMatiere')
  if ($(this).val() === '') {
    volumeMatiere.html('Choisir d\'abord une matière')
  } else {
    $.ajax({
      url: Routing.generate('api_matiere', {'matiere': $(this).val()}),
      success: function (data) {
        const html = 'PPN Officiel => CM ' + data.cmFormation + ' heure(s); TD ' + data.tdFormation + ' heure(s); TP ' + data.tpFormation + ' heure(s); PPN Réalisé/departement => CM ' + data.cmPpn + ' heure(s); TD ' + data.tdPpn + ' heure(s); TP ' + data.tpPpn + ' heure(s);'
        volumeMatiere.html(html)
        $('#cmMaquette').html(data.cmFormation)
        $('#totalHeuresCm').html(0)
        $('#diffCm').html(0)
        $('#tdMaquette').html(data.tdFormation)
        $('#totalHeuresTd').html(0)
        $('#diffTd').html(0)
        $('#tpMaquette').html(data.tpFormation)
        $('#totalHeuresTp').html(0)
        $('#diffTp').html(0)
        $('#tabheures').show()
      }
    })
  }
})


$(document).on('click', '#btnGenereFichier', function (e) {
  e.preventDefault()
  e.stopPropagation()

  const selectedChamps = []
  $('input:checkbox[name=exportChamps]:checked').each(function () {
    selectedChamps.push($(this).val())
  })

  $.ajax({
    url: Routing.generate('export_listing.fr'),
    method: 'POST',
    data: {
      'matiere': $(this).data('matiere'),
      'exportTypeDocument': $('input[type=radio][name=exportTypeDocument]:checked').val(),
      'exportChamps': selectedChamps,
      'exportFormat': $('input[type=radio][name=exportFormat]:checked').val(),
      'exportFiltre': $('input[type=radio][name=exportFiltre]:checked').val()
    },
    success: function (fichier) {

    }
  })
})


//reload si changement d'année
$(document).on('change', '#change_annee_temp_hrs', function (e) {

  window.location = Routing.generate('administration_hrs_index', {annee: $(this).val()})
})

$(document).on('change', '#change_annee_temp_previsionnel', function (e) {
  window.location = Routing.generate('administration_previsionnel_index', {annee: $(this).val()})
})

// $(document).on('click', '.previsionnelModule', function () {
//   var modalPrevisionnel = $('#modalPrevisionnel');
//
//   $.ajax({
//     url: Routing.generate('api_previsionnel_matiere', {'matiere': $(this).data('matiere')}),
//     success: function (data) {
//
//       modalPrevisionnel.empty();
//       var html = '<table class="table table-bordered table-condensed">\n' +
//         '                    <thead>\n' +
//         '                    <tr>\n' +
//         '                        <th class="cm">NB h*</th>\n' +
//         '                        <th class="cm">NB Gr.</th>\n' +
//         '                        <th class="cm">1.5**</th>\n' +
//         '\n' +
//         '                        <th class="previtd">NB h/ Gr.*</th>\n' +
//         '                        <th class="previtd">NB Gr.</th>\n' +
//         '                        <th class="previtd">1.5**</th>\n' +
//         '\n' +
//         '                        <th class="previtp">NB h/ Gr.*</th>\n' +
//         '                        <th class="previtp">NB Gr.</th>\n' +
//         '                        <th class="previtp">1.5**</th>\n' +
//         '                    </tr>\n' +
//         '                    </thead>\n' +
//         '                    <tbody>\n';
//
//       jQuery.each(data, function (index, matiere) {
//         html = html +
//           '                        <tr>\n' +
//           '                            <td colspan="9">\n' +
//           '                                ' + matiere.personnel + '\n' +
//           '                            </td>\n' +
//           '                        </tr>\n' +
//           '                        <tr>\n' +
//           '                            <td>' + matiere.nbHCm + ' h</td>\n' +
//           '                            <td>' + matiere.nbGrCm + '</td>\n' +
//           '                            <td>' + matiere.nbSeanceCm + '</td>\n' +
//           '                            <td class="previtd">' + matiere.nbHTd + ' h</td>\n' +
//           '                            <td class="previtd">' + matiere.nbGrTd + '</td>\n' +
//           '                            <td class="previtd">' + matiere.nbSeanceTd + '</td>\n' +
//           '                            <td class="previtp">' + matiere.nbHTp + ' h</td>\n' +
//           '                            <td class="previtp">' + matiere.nbGrTp + '</td>\n' +
//           '                            <td class="previtp">' + matiere.nbSeanceTp + '</td>\n' +
//           '                        </tr>\n';
//
//       });
//       html = html + '                    </tbody>\n' +
//         '                </table>';
//
//       modalPrevisionnel.append(html);
//     }
//   });
// })
