jQuery(document).ready(function ($) {

	$('.taxonomy-ua_next_camp div[data-name=date] input, .taxonomy-ua_next_camp div[data-name=city] input, .taxonomy-ua_next_camp div[data-name=state] input').on('change', function(e){

		let date = $('div[data-name=date]').find('.input.hasDatepicker').val()
		let city = $('div[data-name=city]').find('.acf-input-wrap > input').val()
		let state = $('div[data-name=state]').find('.acf-input-wrap > input').val()

		$('input#tag-name').val(date + ' - ' + city + ', ' + state)

	})

	// on #create_player_with_ajax submit, do ajax request and return response
	$('.btn-create-player').on('click', function(e){

		e.preventDefault()

		let image = $('#create_player_with_ajax div[data-name=image] input')
		let first_name = $('#create_player_with_ajax div[data-name=first_name] input')
		let last_name = $('#create_player_with_ajax div[data-name=last_name] input')


		let data = {
			action: 'create_player_with_ajax',
			image: $(image).val(),
			first_name: $(first_name).val(),
			last_name: $(last_name).val(),

		}

		$.post(ajaxurl, data, function(response){

			if(response.success){
				$(image).val('')
				$(first_name).val('')
				$(last_name).val('')

				$('#create_player_ajax_response').text(response.data.message)

			} else {

				$('#create_player_ajax_response').text(response.data.message)

			}

		})

	})

})