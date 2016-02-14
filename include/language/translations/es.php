<?php

$lang['sorry'] = 'Perdon';
$lang['after_release'] = 'despues del lanzamiento';
$lang['position'] = 'Posicion';
$lang['team'] = 'Equipo';
$lang['points'] = 'Puntos';
$lang['points_short'] = 'pts';
$lang['country'] = 'Pais';
$lang['solved'] = 'Solucionado';
$lang['home'] = 'Home';
$lang['profile'] = 'Perfil';
$lang['scores'] = 'Puntuaciones';
$lang['log_in'] = 'Log in';
$lang['log_out'] = 'Log out';
$lang['close'] = 'Cerrar';
$lang['error'] = 'Error';
$lang['profile_settings'] = 'Opciones Perfil';
$lang['view_public_profile'] = 'Ver perfil publico';
$lang['hint'] = 'Pista';
$lang['hints'] = 'Pistas';
$lang['no_hints_available'] = 'No hay pistas disponibles por el momento.';
$lang['challenge'] = 'Reto';
$lang['added'] = 'Añadido';
$lang['challenges'] = 'Retos';
$lang['category'] = 'Categoria';
$lang['ctf_empty'] = '¡Tu CTF esta muy vacio! Empieza añadiendo una categoria usando la consola de administracion.';
$lang['available_in'] = 'Disponible en';
$lang['cat_unavailable'] = 'Categoria no disponible';

$lang['two_factor_auth'] = 'Autentificacion de dos-factores';
$lang['two_factor_auth_required'] = 'Autentificacion de dos-factores requerida';
$lang['enable_two_factor_auth'] = 'Activar autentificacion de dos-factores';
$lang['disable_two_factor_auth'] = 'Desactivar utentificacion de dos-factores';
$lang['generate_codes'] = 'Generar codigos';
$lang['using_totp'] = 'usando TOTP';
$lang['scan_with_totp_app'] = 'Escanea con tu TOTP app';
$lang['authenticate'] = 'Autenticar';

$lang['save_changes'] = 'Guardar cambios';
$lang['reset_password'] = 'Resetear Contraseña';
$lang['choose_password'] = 'Escoger Contraseña';
$lang['password'] = 'Contraseña';
$lang['email_password_on_signup'] = 'Un email de confirmacion con una contraseña aleatoria sera enviado a la direccion de correo seleccionada.';

$lang['register'] = 'Registrar';
$lang['register_your_team'] = 'Registra tu equipo';
$lang['account_signup_information'] = 'Tu equipo comparte una cuenta. {password_information}';
$lang['team_name'] = 'Nombre del equipo';
$lang['select_team_type'] = 'Por favor selecciona un tipo de equipo';
$lang['registration_closed'] = 'Los registros estan cerrados actualmente, pero aun puedes tu <a href="interest"></a>interes por futuros eventos.';
$lang['please_fill_details_correctly'] = 'Por favor rellena todos los campos correctamente.';
$lang['invalid_team_type'] = 'Eso no parece un tipo de equipo valido.';
$lang['team_name_too_long_or_short'] = 'El nombre de tu equipo es demasiado largo o demasiado corto.';
$lang['email_not_whitelisted'] = 'La direccion de correo no esta en la whitelist. Por favor seleccion una direccion de correo que este en la whitelist o contacta a los organizadores.';
$lang['user_already_exists'] = 'Una cuenta para este nombre de equipo o direccion de correo ya existe.';
$lang['signup_successful'] = 'Registrado exitosamente';
$lang['signup_successful_text'] = '¡Gracias por registrarte! Tu direccion de correo es: {email}. Asegurate de comprobar tu carpeta de spam ya que nuestros correos pueden llegarte alli. ¡Estate atento a las novedades!';
$lang['your_password_is'] = 'Tu contraseña es';
$lang['your_password_was_set'] = 'Tu contraseña fue escogida por ti durante el registro.';

$lang['signup_email_subject'] = '{site_name} detalles de cuenta';
$lang['signup_email_success'] =
    '{team_name}, tu registro en {site_name} fue exitoso.' .
    "\r\n" .
    "\r\n" .
    '{signup_email_availability}' .
    "\r\n" .
    "\r\n" .
    '{signup_email_password}' .
    "\r\n" .
    "\r\n" .
    '¡Estate atento a las novedades!' .
    "\r\n" .
    "\r\n" .
    'Atentamente,' .
    "\r\n" .
    '{site_name}'
;
$lang['signup_email_account_availability_message_login_now'] = 'Ahora puedes acceder utilizando tu direccion de correo y contraseña.';
$lang['signup_email_account_availability_message_login_later'] = 'Una vez que la competicion comience, por favor usa esta direccion de correo para acceder.';

$lang['register_interest'] = 'Interes en registrarse';
$lang['register_interest_text'] = 'Probablemente organicemos mas CTFs en el futuro. Introduce tu direccion de correo si estas interesado en saber de nosotros sobre futuras competiciones. No te enviaremos spam. Tu direccion de correo no sera compartida con terceras partes.';

$lang['expression_of_interest'] = 'Expresion de interes';
$lang['recruitment_text'] = '¿Te gustan nuestros sponsors? Estan buscando gente. Por favor rellena el formulario de abajo para recibir informacion laboral. Cada miembro del equipo puede rellenar el formulario individualmente. No compartiremos tu informacion con nadie mas que con nuestros sponsors. No te enviaremos spam. Solo las direcciones en este formulario seran compartidas.';
$lang['name_optional'] = 'Nombre (opcional)';
$lang['city_optional'] = 'Ciudad (opcional)';

$lang['email_address'] = 'Direccion de correo';
$lang['password'] = 'Contraseña';
$lang['name_nick'] = 'Nombre / nombre del equipo / nick';
$lang['remember_me'] = 'Recuerdame';
$lang['forgotten_password'] = 'He olvidado mi contraseña';

$lang['please_request_view'] = 'Por favor solicita una vista';
$lang['please_request_page'] = 'Por favor solicita una pagina para ver';
$lang['please_supply_country_code'] = 'Por favor introduce un codigo de pais valido';
$lang['not_a_valid_link'] = 'Ese no es un link valido.';
$lang['not_a_valid_email'] = 'Eso no parece una direccion de correo. Por favor vuelve al formulario y comprueba los datos.';
$lang['please_select_country'] = 'Por favor selecciona un pais';

$lang['no_file_found'] = 'Ningun archivo encontrado con ese ID.';
$lang['invalid_team_key'] = 'Llave de equipo invalida.';
$lang['user_not_enabled'] = 'Este usuario no esta habilitado, por lo que no puede descargar archivos.';
$lang['file_not_available'] = 'Este archivo aun no esta disponible.';

$lang['challenge_details'] = 'Detalles del reto';
$lang['no_challenge_for_id'] = 'Ningun reto encontrado con este ID, o el reto no es publico';
$lang['no_category_for_id'] = 'Ninguna categoria encontrada con este ID, o la categoria no es publica';
$lang['challenge_not_available'] = 'Este reto aun no esta disponible';
$lang['challenge_not_solved'] = 'Este reto aun no ha sido resuelto por ningun equipo.';
$lang['challenge_solved_by_percentage'] = 'Este reto ha sido resuelto por el {solve_percentage}% de los usuarios participantes activos.';

$lang['challenge_solved_first'] = '¡Primero en resolver el reto!';
$lang['challenge_solved_second'] = '¡Segundo en resolver el reto!';
$lang['challenge_solved_third'] = '¡Tercero en resolver el reto!';

$lang['correct_flag'] = '¡Flag correcta, eres increible!';
$lang['incorrect_flag'] = 'Flag incorrecta, intentalo otra vez.';
$lang['submission_awaiting_mark'] = 'Tu solicitud esta pendiente de revision.';
$lang['please_enter_flag'] = 'Por favor introduce la flag para el reto:';
$lang['submit_flag'] = 'Enviar flag';
$lang['no_remaining_submissions'] = 'No te quedan mas oportunidades. Si has enviado algo por error, por favor contacta a los organizadores.';

$lang['no_category_with_id'] = 'Ninguna categoria encontrada con ese ID';

$lang['cat_unavailable_explanation'] = 'Esta categoria no esta disponible. Esta abierta desde {available_from} ({available_from_time_remaining} from now) hasta {available_until} ({available_until_time_remaining} desde ahora)';

$lang['hidden_challenge_worth'] = 'Reto oculto valorado en {pts}pts';

$lang['available_in'] = 'Disponible en {available_in} (desde {from} hasta {to})';
$lang['minimum_time_between_submissions'] = 'Tiempo de espera minimo de {time} entre envios.';
$lang['num_submissions_remaining'] = '{num_remaining} envios restantes.';
$lang['time_remaining'] = '{time} restante';

$lang['challenge_relies_on'] = 'Los detalles para este reto apareceran solamente despues de que {relies_on_link} en la categoria {relies_on_category_link} sea resuelta (por cualquier equipo).';

$lang['no_reset_data'] = 'Ninguna informacion de reseteo encontrada.';

$lang['scoreboard'] = 'Marcador';
$lang['first_solvers'] = 'Primeros resolviendo';
$lang['percentage_solvers'] = 'Resuelto por';
$lang['unsolved'] = 'Sin resolver';

$lang['user_details'] = 'Detalles de usuario';
$lang['no_user_found'] = 'Nigun usuario encontrado con ese ID';
$lang['non_competing_user'] = 'Este usuario esta marcado como no-competidor.';
$lang['no_information'] = 'No hay informacion';
$lang['no_solves'] = '¡Este usuario aun no ha resuelto ningun reto!';
$lang['no_exceptions'] = 'No hay excepciones para este usuario';
$lang['solved_challenges'] = 'Retos resueltos';
$lang['total_solves'] = 'Total:';
$lang['no_challenges_solved'] = '¡Ningun reto resuelto, aun!';

$lang['action_success'] = '¡Exito!';
$lang['action_failure'] = '¡Fallo!';
$lang['action_something_went_wrong'] = '¡Algo ha salido mal! Probablemente la accion que has intentado realizar ha fallado.';
$lang['generic_error'] = 'Algo ha salido mal.';

$lang['year'] = 'año';
$lang['month'] = 'mes';
$lang['day'] = 'dia';
$lang['hour'] = 'hora';
$lang['minute'] = 'minuto';
$lang['second'] = 'segundo';
$lang['append_to_time_to_make_plural'] = 's';

$lang['user_class_user'] = 'Usuario';
$lang['user_class_moderator'] = 'Moderador';
$lang['user_class_unknown'] = 'Clase de usuario desconocida';

$lang['manage'] = 'Administrar';
$lang['add_news_item'] = 'Añadir noticia';
$lang['list_news_item'] = 'Listar noticias';
$lang['news'] = 'Noticias';

$lang['categories'] = 'Categorias';
$lang['add_category'] = 'Añadir categoria';
$lang['list_categories'] = 'Listar categorias';

$lang['add_challenge'] = 'Añadir reto';
$lang['list_challenges'] = 'Listar retos';

$lang['submissions'] = 'Solicitudes';
$lang['list_submissions_in_need_of_marking'] = 'Listar solicitudes que necesitan ser revisadas';
$lang['list_all_submissions'] = 'Listar todas las solicitudes';

$lang['users'] = 'Usuarios';
$lang['list_users'] = 'Listar usuarios';
$lang['user_types'] = 'Tipos de usuario';
$lang['add_user_type'] = 'Añadir tipo de usuario';
$lang['list_user_types'] = 'Listar tipos de usuario';

$lang['signup_rules'] = 'Reglas de registro';
$lang['list_rules'] = 'Lista de reglas';
$lang['new_rule'] = 'Nueva regla';
$lang['test_rule'] = 'Probar regla';

$lang['single_email'] = 'Email unico';
$lang['email_all_users'] = 'Email a todos los usuarios';

$lang['new_hint'] = 'Nueva pista';
$lang['list_hints'] = 'Listar pistas';

$lang['dynamic_content'] = 'Contenido dinamico';
$lang['new_menu_item'] = 'Nuevo menu';
$lang['list_menu_items'] = 'Listar menus';
$lang['menu'] = 'Menu';
$lang['new_page'] = 'Nueva pagina';
$lang['list_pages'] = 'Listar paginas';
$lang['pages'] = 'Paginas';

$lang['exceptions'] = 'Excepciones';
$lang['list_exceptions'] = 'Listar excepciones';
$lang['clear_exceptions'] = 'Limpiar excepciones';

$lang['search'] = 'Buscar';
