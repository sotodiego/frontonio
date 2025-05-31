jQuery(document).ready(function(e){

    //InnerTbale
    $(".interTable").each((k,v) => window[$(v).attr('id')] = interTable(window[$(v).attr('id')]));

    //Chat
    $(".chatTable").each((k,v) => window[$(v).attr('id')] = chatTable(window[$(v).attr('id')]));

    //galeria
    $(".videoTable").each((k,v) => window[$(v).attr('id')] = videoTable(window[$(v).attr('id')]));

    //dataTables
    if($(".dataTables").length) $(".dataTables").each((k,v) => {
        let _table_ =   window[$(v).attr('id')];
        _table_.ajax.url = AjaxUrl + _table_.ajax.url + "&unik=" + window.unik;
        _table_.language = datatablet_es;
        window[$(v).attr('id')] = $("#" + $(v).attr('id')).DataTable(_table_);
    })

    //PayData
    if($("#pay_data").length){
        $("#pay_data").on("click", ".edit_pago", function(e) {
            e.preventDefault();
            loading("cargando");
            let _id = $(this).data("id");
            let _uid = $(this).data("uid");
            SendAjax("payments", 4, {_id: _uid, _payid: _id}).then(rsp => {
                loading_end();
                if(rsp.r){
                    let _data = rsp.data;
                    let contenido = $("<div />", {class: 'row'});

                    if(_data.type == 1 && _data.estado == 1){
                        let _tabla = $("<tabla />", {class: 'col s12'});
                        _tabla.append(`<tr><th>Cuota:</th><td>${window.pay_cuotas[_data.cuota]}</td></tr>`);
                        _tabla.append(`<tr><th>Fecha inicio:</th><td>${_data.fecha_inicio}</td></tr>`);
                        _tabla.append(`<tr><th>Fecha fin:</th><td>${_data.fecha_final}</td></tr>`);
                        _tabla.append(`<tr><th>Fecha de pago:</th><td>${_data.fecha_pago}</td></tr>`);
                        _tabla.append(`<tr><th>Notas:</th><td>${_data.nota}</td></tr>`);
                        contenido.append(_tabla);
                        modalsp("Cuota pagada", contenido, null, null, 3);
                    }else{
                        contenido.append(modalspElement(2, "Cuota", "receipt_long", "_user_cuota", _data.cuota, window.pay_cuotas, 1));
                        contenido.append(modalspElement(4, "Inicio del pago", "calendar_month", "_user_ini", _data.fecha_inicio, "", 1));
                        contenido.append(modalspElement(4, "Final del pago", "calendar_month", "_user_end", _data.fecha_final, "", 1));
                        contenido.append(modalspElement(4, "Fecha de pago", "calendar_month", "_user_pag", _data.fecha_pago, "", 1));
                        contenido.append(modalspElement(10, "Notas", "description", "_user_note", _data.nota, "", 1));

                        if(_data.type == 1){
                            contenido.append(modalspElement(3, "Estado", "receipt_long", "_user_sts", _data.estado, {0: 'Pendiente', 1: 'Pagado'}, 1));
                        }
                        let modal = modalsp("Editar Pago", contenido, () => {
                            let data = {
                                "_ide": _id,
                                "_id": _uid,
                                "_ini": $("#_user_ini").val(),
                                "_end": $("#_user_end").val(),
                                "_cuota": $("#_user_cuota").val(),
                                "_note": $("#_user_note").val(),
                                "_pag": $("#_user_pag").val(),
                                "_sts": $("#_user_sts").val(),
                            }
                            if(!data._ini) return alert('Por favor seleccione la fecha de inicio del pago');
                            if(!data._end) return alert('Por favor seleccione la fecha de fin del pago');
                            if(!data._cuota) return alert('Por favor seleccione la cuota');
                            loading("Creando");
                            SendAjax("payments", 5, data).then(rsp => {
                                loading_end();
                                alert(rsp.m, rsp.r);
                                window.pay_data.ajax.reload(null, false);
                                if(rsp.r) modal.close();
                            }).catch(error => {
                                loading_end();
                                alert(error);
                            });
                        }, "Guardar", 3);
                        modal.open();
                    }
                }else{
                    alert(rsp.m);
                }                
            }).catch(error => {
                loading_end();
                alert(error);
            });
        });
    }

    //Back
    if('backurl' in window){
        $(".content_header .back").append(`<a href='${base_url}/${window.backurl}' class='material-symbols-outlined'>arrow_back_ios</a>`);
    }

    //MenuInferior
    if('submenu' in window){
        botoneraPwaPantallas(window.submenu);
    }else{
        resizeScreenPwa();
    }

    //MenuInferior
    if('breadcrumbs' in window){
        breadCrumbs(window.breadcrumbs);
    }

    //Asistencia
    if('nextclase' in window){
        let contenido = $("<div />", {class: 'row'});
        contenido.append(`<div class='col s12'><p>¿Podrás asistir a tu clase de las ${window.nextclase.fecha} del grupo ${window.nextclase.grupo_nombre} el día de hoy?</p></div>`);
        contenido.append(modalspElement(3, "", "", "_asistencia", "", {1: 'Si', 2: 'No' }, 1));
        let modal = modalsp("Próxima clase", contenido, () => {
            loading("Enviando");
            let data = {
                "_id": window.nextclase.id,
                "_asistencia": $("#_asistencia").val(),
            }
            SendAjax("asitencia_grupo_confirm", 0, data).then(rsp => {
                loading_end();
                alert(rsp.m, rsp.r);
                if(rsp.r) modal.close();
            }).catch(error => {
                loading_end();
                alert(error);
            });
        }, "Confirmar", 3);
        modal.open();
    }

    //pago
    if('paypend' in window){

        const contenido2 = $("<div />", {class: 'row'});
        contenido2.append(`<div class='input-field col s12'><p class='m-0'>Tiene pendiente el pago de ${window.paypend.cant} € por la cuota de "${window.paypend.cuot}".</p><p>¿Quiere pagar la cuota?</p></div>`);
        const modal2 = modalsp("Cuota", contenido2, () => {
            modal.open();
            modal2.close();
        }, "Pagar", 3);

        const contenido = $("<div />", {class: 'row'});
        contenido.append(modalspElement(1, "Nombre", "person", "_fullname", window.paypend.name, null, 1));
        contenido.append('<div class="input-field col s12"><div id="card-element"></div></div>');
        const modal = modalsp("Pago de cuota", contenido, () => {
            if($("#_fullname").val().length < 4) return alert("Escriba a nombre de quien se encuentra la tarjeta");
            loading("Pagando");

            stripe.confirmCardPayment(window.paypend.id, {
              payment_method: {
                card: card,
                billing_details: {
                  name: $("#_fullname").val(),
                },
              }
            }).then(function(result) {
              if (result.error) {
                alert(result.error.message);
              } else {
                if (result.paymentIntent.status === 'succeeded') {
                  let data = {
                        "_pago": JSON.stringify(result),
                        "_pid" : window.paypend.pago
                    }
                    SendAjax("payments", 3, data).then(rsp => {
                        console.log(rsp);
                        loading_end();
                        alert(rsp.m, rsp.r);
                        if(rsp.r) modal.close();
                    }).catch(error => {
                        loading_end();
                        alert(error);
                    });
                }else{
                    console.log(result);
                }
              }
            });
        }, "Enviar", 3);

        var style = {
          base: {
            iconColor: '#ffffff',
            color: "#ffffff",
            fontWeight: 400,
            fontFamily: "Muli, sans-serif",
            fontSize: "14px",
            fontSmoothing: "antialiased",
            '::placeholder': {
              color: "#ffffff"
            },
            ':-webkit-autofill': {
              color: '#ffffff',
            },
          },
          invalid: {
            iconColor: '#FFC7EE',
            color: '#FFC7EE',
          }
        };

        const stripe = Stripe(StripePub);
        const elements = stripe.elements();        
        const card = elements.create('card', {style: style});
        card.mount('#card-element');
        modal2.open();
    }

    //extrauser
    if('rsol' in window){
        $.each(window.rsol, (k,sol) => {
            let falta = sol.leaders + sol.followers - sol.inscritos;
            let tipo = sol.leaders > 0 ? 'leader' : 'follower';

            let contenido = $("<div />", {class: 'row'});
            contenido.append(`<div class='col s12'><p>El grupo ${sol.nombre} necesita tu ayuda para la clase de las ${sol.fecha} el día de hoy, necesitamos ${falta} ${tipo}${falta > 1 ? 's' : ''}<br> ¿Te gustaría unirte a la clase?</p></div>`);
            contenido.append(modalspElement(3, "", "", "_asistencia"+sol.id, "", {1: 'Si', 2: 'No' }, 1));
            let modal = modalsp("¿Te gustaría ayudar?", contenido, () => {
                setcookie("RID"+sol.id, true);
                let data = {
                    "_id": sol.id,
                    "_asistencia": $("#_asistencia"+sol.id).val(),
                }
                if(data._asistencia == 2){
                    alert("Gracias por tu tiempo y lamentamos que no puedas asistir", true);
                    modal.close();
                    return;
                }
                loading("Enviando");
                SendAjax("asitencia_grupo_confirm", 1, data).then(rsp => {
                    loading_end();
                    alert(rsp.m, rsp.r);
                    modal.close();
                }).catch(error => {
                    loading_end();
                    alert(error);
                });
            }, "Confirmar", 3);
            modal.open();
        })
    }

    function setcookie(nombre, valor) {
        var fechaExpiracion = new Date();
        fechaExpiracion.setTime(fechaExpiracion.getTime() + (24 * 60 * 60 * 1000));
        var expira = "expires=" + fechaExpiracion.toUTCString();
        document.cookie = nombre + "=" + valor + ";" + expira + ";path=/";
    }

    //Invitaciones
    $(".show_invitation_pwa").click(function(e){
        let id = $(this).attr('id');
        if(id){
            $(".content_invitation[id='"+id+"']").fadeIn();
        }else{  
            alert('Lo sentimos, ha ocurrido un error al obtener la información. Por favor inténtelo de nuevo más tarde');
        }
    });

    $(".content_invitation > span").click(function(e){
        let id = $(this).attr('id');
        if(id){
            $(".content_invitation[id='"+id+"']").fadeOut();
        } 
    });

    $(".btns_invitation").click(function(e){
        let id = $(this).attr('id');
        if(id){
            if($(this).hasClass('decline')){
                alert_confirm('¿Está seguro de rechazar la invitación?', ()=>{
                    loading('Cargando');
                    SendAjax("pwa", 1, {id: id}).then(rsp => {
                        loading_end();
                        console.log(rsp)
                        if(rsp.r){
                            alert(rsp.m, true);
                            $(".contain_row_invitation[id='"+id+"']").remove();
                            if(!$(".contain_row_invitation").length) $(".container_academies_pwa.invitation").remove();
                        }else{
                            alert(rsp.m);
                        }
                    }).catch(error => {
                        loading_end();
                        alert(error);
                    });
                });
            }else{
                alert_confirm('¿Está seguro de aceptar la invitación?', ()=>{
                    loading('Cargando');
                    SendAjax("pwa", 2, {id: id}).then(rsp => {
                        loading_end();
                        console.log(rsp)
                        if(rsp.r){
                            alert(rsp.m, true);
                            if(rsp.d && rsp.d.length){
                                $.each(rsp.d, (k,v)=>{
                                    let rowAcademy = $(`<div class="row_academy_pwa">
                                    <div class="l">
                                        <img src="${v.logo}" alt="Academia ${v.nombre}" />
                                    </div>
                                    <div class="r">
                                        <h3>${v.nombre}</h3>
                                    </div>
                                    </div>`);
                                    $(".container_academies_pwa.academies .list_academies_pwa").append(rowAcademy);
                                });
                            }else{
                                setTimeout(()=>{
                                    window.location.reload();
                                }, 1000);
                            }
                            $(".contain_row_invitation[id='"+id+"']").remove();
                            if(!$(".contain_row_invitation").length) $(".container_academies_pwa.invitation").remove();
                        }else{
                            alert(rsp.m);
                        }
                    }).catch(error => {
                        loading_end();
                        alert(error);
                    });
                });
            }
        }else{
            alert('Lo sentimos, ha ocurrido un error al obtener la información. Por favor inténtelo de nuevo más tarde'); 
        }
    });

    //Grupos acciones
    if($("#group_academy").length){
        

        $('.timepicker').timepicker({
            twelveHour: false,
            i18n: {
                cancel: 'Cancelar',
                clear: 'Limpiar',
                done: 'Seleccionar',
            }
        });

        $("body").on('click', '.select_days_class', function(e){
			e.preventDefault();
			let day = $(this).attr('di');
			$(this).toggleClass('active');
			if(day){
				let theVal = $("#days_class_group").val();
				if(theVal){
					theVal = theVal.split(',');
					if(theVal.includes(day)){
						theVal = theVal.filter((n)=>{return n !== day});
					}else{
						theVal.push(day);
					}
				}else{
					theVal = day;
				}
				$("#days_class_group").val(theVal);
			}
		});

        $("body").on('click', '.select_teacher_group', function(e){
            let tch = $(this).attr('tch');
            if(tch){
                loading("Cargando");
                SendAjax("grupos_academia", 1).then(rsp => {
                    loading_end();
                    console.log(rsp)
                    if(rsp.r){
                        let teachers = {};
                        if(rsp.t && rsp.t.length){
                            $.each(rsp.t, (k,v)=>{
                                let nombre = (v.nombre && v.apellido) ? (v.nombre+' '+v.apellido) : v.correo;
                                teachers[v.id] = nombre;
                            });
                            let value = ($("#group_teacher_"+tch).attr('realval')) ? $("#group_teacher_"+tch).attr('realval') : '';
                            let contenido = $("<div />", {class: 'row'});
                            contenido.append(modalspElement(2, "Profesores", "event_note", "teachers_group_select", value, teachers, 1));
                            let modal = modalsp("Profesores", contenido, () => {
                                let data = {
                                    "profesores": $("#teachers_group_select").val(),
                                }
                                if(data.profesores){
                                    $(`#group_teacher_${tch}`).val(teachers[data.profesores]);
                                    $(`#group_teacher_${tch}`).attr('realval', data.profesores);
                                    $(`<i class="material-symbols-outlined quit_teacher_group" tch="${tch}">close</i>`).insertAfter($(`#group_teacher_${tch}`));
                                    modal.close();
                                }else{
                                    alert('Por favor selecciona un profesor para asignar');
                                }
                            }, "Asignar", 3, );
                            modal.open();
                        }else{
                            alert("Sin profesores");
                        }
                    }else{
                        alert(rsp.m);
                    }
                }).catch(error => {
                    loading_end();
                    alert(error);
                });
            }else{
				alert("Lo sentimos, ha ocurrido un problema al obtener la información");
            }
        });

        $("body").on('click', '.quit_teacher_group', function(e){
            let tch = $(this).attr('tch');
            if(tch){
                $("#group_teacher_"+tch).attr('realval', '');
                $("#group_teacher_"+tch).val('');
                $(this).remove();
            }
        });

        $("#save_data_group").click(function(e){
            let port = 2;
            const dataSaveGroup = {
                nombre: $("#name_group").val(),
                dias: $("#days_class_group").val(),
                p1: $("#group_teacher_1").attr('realval'),
                p2: $("#group_teacher_2").attr('realval'),
                hora: $("#shcedule_group_class").val(),
            };
            let loadingMsgs = 'Creando grupo';
            if($(this).hasClass('edit')){
                port = 3;
                dataSaveGroup.id = $("#id_edit_group").val();
                loadingMsgs = 'Editando grupo';
            }
            if(dataSaveGroup.nombre){
                dataSaveGroup.nombre = dataSaveGroup.nombre.trim(); 
                if(dataSaveGroup.p1 && dataSaveGroup.p2){
                    if(dataSaveGroup.p1 == dataSaveGroup.p2){
                        alert('No puedes asignar al mismo profesor dos veces en un grupo');
                        return;
                    }
                }
                loading(loadingMsgs);
                SendAjax("grupos_academia", port, {data: JSON.stringify(dataSaveGroup)}, ["group_imagen_file"]).then(rsp => {
                    loading_end();
                    console.log(rsp)
                    if(rsp.r){
                        window.location.href = base_url+window.backurl;
                    }else{
                        alert(rsp.m);
                    }
                }).catch(error => {
                    loading_end();
                    alert(error);
                });
            }else{
                alert('Por favor asigna un nombre para el grupo');
            }
        });

        $("#group_imagen_file").change(function(){
            const selectedFile = this.files[0];
            console.log(selectedFile)
            if(selectedFile){
                const fileName = selectedFile.name;
                if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png')) {
                    $('#fileError').text(''); // Borra cualquier mensaje de error previo.
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('.img_to_insert_grupo img').attr('src', e.target.result).show();
                        $('.img_to_insert_grupo').addClass('active');
                    };
                    reader.readAsDataURL(selectedFile);
                }else {
                    alert('El archivo debe ser una imagen en formato JPG o PNG.');
                }
            }
        });
    }

    if($("#save_data_group").length){
        //Días de la semana
        let daysWeek = $("#days_class_group").val();
        if(daysWeek){
            daysWeek = daysWeek.split(',');
            $.each(daysWeek, (k,v)=>{
                $(".select_days_class[di='"+v+"']").addClass('active');
            });
        }
        //Días de la semana
    }

    $(".closeinfo").click(function(e) {
        e.preventDefault();
        var d = new Date();
        d.setTime(d.getTime() + (20*365*24*60*60*1000));
        var expires = "; expires="+d.toUTCString();
        document.cookie = "xi=true"+expires+"; path=/";
        $(".mssgs_create_group").slideToggle();
    })

    $(".closemensaje").click(function(e) {
        e.preventDefault();
        var d = new Date();
        d.setTime(d.getTime() + (20*365*24*60*60*1000));
        var expires = "; expires="+d.toUTCString();
        document.cookie = "mi=true"+expires+"; path=/";
        $(".rows.f2").slideToggle();
    })

    //Perfil usuarios
    if($("#save_data_user_ac").length){
        const eidtUserProfile = (data)=>{
            loading('Guardando información');
            SendAjax("usuarios_academia", 4, {data: JSON.stringify(data)}, ["img_profile_user"]).then(rsp => {
                loading_end();
                console.log(rsp)
                if(rsp.r){
                    alert(rsp.m, true);
                }else{
                    alert(rsp.m);
                }
            }).catch(error => {
                loading_end();
                alert(error);
            });
        }

        $("#img_profile_user").change(function(){
            const selectedFile = this.files[0];
            if(selectedFile){
                const fileName = selectedFile.name;
                if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $("label[for='img_profile_user'] span").empty();
                        const imgPrfofile = $(`<img src='${e.target.result}' />`);
                        $("label[for='img_profile_user'] span").append(imgPrfofile);
                    };
                    reader.readAsDataURL(selectedFile);
                }else {
                    alert('El archivo debe ser una imagen en formato JPG o PNG.');
                }
            }
        });

        $("#save_data_user_ac").click(function(e){
            e.preventDefault();
            const dataUserProfile = {
                nombre: $("#name_user").val(),
                apellido: $("#last_name_user").val(),
                telefono: $("#tel_user").val(),
                correo: $("#mail_user").val(),
                pass1: $("#pass1").val(),
                pass2: $("#pass2").val(),
            };
            if(dataUserProfile.nombre && dataUserProfile.apellido && dataUserProfile.correo){
                if(dataUserProfile.pass1 || dataUserProfile.pass2){
                    if(!dataUserProfile.pass1 || !dataUserProfile.pass2){
                        alert('Para cambiar tu contraseña, asegúrate de llenar ambos campos relacionados con la nueva contraseña');
                        return;
                    }
                    if(dataUserProfile.pass1 !== dataUserProfile.pass2){
                        alert('Las contraseñas no son iguales');
                        return;
                    }
                    if(dataUserProfile.pass1.length < 8){
                        alert('Las contraseñas deben tener al menos 8 dígitos');
                        return;
                    }
                    if(!(/[0-9]/.test(dataUserProfile.pass1))){
                        alert('Las contraseñas deben tener al menos 1 número');
                        return;
                    }
                    if(!(/[a-z]/.test(dataUserProfile.pass1))){
                        alert('Las contraseñas deben tener al menos 1 letra minúscula');
                        return;
                    }
                    if(!(/[A-Z]/.test(dataUserProfile.pass1))){
                        alert('Las contraseñas deben tener al menos 1 letra mayúscula');
                        return;
                    }
                    if(!(/[!@#$%^&*(),.?":{}|<>]/.test(dataUserProfile.pass1))){
                        alert('Las contraseñas deben tener al menos 1 carácter especial como !@#$%^&*(),.?":{}|<>');
                        return;
                    }               
                }
                if($("#mail_user").val() !== $("#mail_user").attr('oldval')){
                    alert_confirm('Cuando editas tu dirección de correo electrónico, tu nombre de usuario también se actualizará automáticamente', ()=>{
                        eidtUserProfile(dataUserProfile);
                    });
                }else{
                    eidtUserProfile(dataUserProfile);
                }
            }else{
                alert('Por favor complete todos los campos requeridos');
            }
        });
    }

    $("#menu_user_ac").click(function(e){
        $("#menu_ul_user_ac").toggleClass('active');
    });

    $("body").on("click", "#breadcrumbs_pwa a:last-child", function(e){
        e.preventDefault();
    });

    //Perfil public usuarios
    if($("#save_data_user_alt").length){
        $("#img_profile_user").change(function(){
            const selectedFile = this.files[0];
            if(selectedFile){
                const fileName = selectedFile.name;
                if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $("label[for='img_profile_user'] span").empty();
                        const imgPrfofile = $(`<img src='${e.target.result}' />`);
                        $("label[for='img_profile_user'] span").append(imgPrfofile);
                    };
                    reader.readAsDataURL(selectedFile);
                }else {
                    alert('El archivo debe ser una imagen en formato JPG o PNG.');
                }
            }
        });
        $(".galley_img input").change(function(){
            const selectedFile = this.files[0];
            const idImg = this.id;
            if(selectedFile){
                const fileName = selectedFile.name;
                if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $(`#${idImg}_img`).attr('src', e.target.result);
                    };
                    reader.readAsDataURL(selectedFile);
                }else {
                    alert('El archivo debe ser una imagen en formato JPG o PNG.');
                }
            }
        });
        $("#save_data_user_alt").click(function(e) {
            e.preventDefault();
            SendAjax("usuarios_academia", 8, {desc: $("#user_desc").val()}, ["img_profile_user", "img_gallery_user_1", "img_gallery_user_2", "img_gallery_user_3", "img_gallery_user_4", "img_gallery_user_5", "img_gallery_user_6", "img_gallery_user_7", "img_gallery_user_8", "img_gallery_user_9"]).then(rsp => {
                loading_end();
                console.log(rsp);
                if(rsp.r) alert(rsp.m, true);
                else alert(`${rsp.m}:<br><br>${rsp.e.join('<br>')}`);
            }).catch(error => {
                loading_end();
                alert(error);
            });
        })
    }

    $(".img_gallery_perfil img").click(function(e){
        e.preventDefault();
        $(".md_lightbox_cont img").attr("src", $(this).attr("src"));
        $("#md_lightbox_input").click();
    })

});