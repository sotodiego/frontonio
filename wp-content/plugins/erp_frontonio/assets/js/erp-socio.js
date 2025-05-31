jQuery(document).ready(function($) {

    let aje_noc =  $("#aje_noc").val()
    window.unik = aje_noc;














    function editProfile(op, data, files = []) {
        loading('Guardando información');
        SendAjax("perfil_socio", op, { data: JSON.stringify(data) }, files)
          .then(rsp => {
            loading_end();
            alert(rsp.m, rsp.r);
          })
          .catch(err => {
            loading_end();
            alert(err);
          });
    }

    // Guardar perfil privado
    $("#save_data_privado_socio").click(function(e){
    e.preventDefault();
    const $f = $("#form_edit_profile_privado");
    const data = {};

    DATOS_OBLIGATORIOS_PRIVADOS.forEach(campo => {
        data[campo] = $f.find(`#${campo}`).val().trim();
    });
    
    data.pass1 = $f.find('#pass1').val();
    data.pass2 = $f.find('#pass2').val();

    if ((data.pass1||data.pass2)) {
        if (data.pass1 !== data.pass2) return alert('Las contraseñas no coinciden.');
        if (data.pass1.length < 8) return alert('La contraseña debe tener al menos 8 caracteres.');
        if (!/[0-9]/.test(data.pass1))   return alert('La contraseña debe contener al menos un número.');
        if (!/[a-z]/.test(data.pass1))   return alert('La contraseña debe contener al menos una letra minúscula.');
        if (!/[A-Z]/.test(data.pass1))   return alert('La contraseña debe contener al menos una letra mayúscula.');
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(data.pass1)) return alert('La contraseña debe contener al menos un carácter especial.');
    }
    editProfile(0, data, []);
    });

    // Guardar perfil público
    $("#save_data_publico_socio").click(function(e){
    e.preventDefault();
    const $f = $("#form_edit_profile_publico");
    const data = {};

    DATOS_OBLIGATORIOS_PUBLICOS.forEach(campo => {
        data[campo] = $f.find(`#${campo}`).val().trim();
    });

    editProfile(1, data, ["logo_empresa","foto_socio"]);
    });
    
    $("#foto_socio").change(function(){
        const selectedFile = this.files[0];
        if(selectedFile){
            const fileName = selectedFile.name;
            if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $("label[for='foto_socio'] span").empty();
                    const imgPrfofile = $(`<img src='${e.target.result}' />`);
                    $("label[for='foto_socio'] span").append(imgPrfofile);
                };
                reader.readAsDataURL(selectedFile);
            }else {
                alert('El archivo debe ser una imagen en formato JPG o PNG.');
            }
        }
    });

    $("#logo_empresa").change(function(){
        const selectedFile = this.files[0];
        if(selectedFile){
            const fileName = selectedFile.name;
            if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $("label[for='logo_empresa'] span").empty();
                    const imgPrfofile = $(`<img src='${e.target.result}' />`);
                    $("label[for='logo_empresa'] span").append(imgPrfofile);
                };
                reader.readAsDataURL(selectedFile);
            }else {
                alert('El archivo debe ser una imagen en formato JPG o PNG.');
            }
        }
    });














	if($("#list_facturas").length){
		//General
		const action = "facturas_socios";
		//Listar
		_table_.ajax.url = `${AjaxUrl}${_table_.ajax.url}&${action}=0&unik=${aje_noc}`;
		_table_.language = datatablet_es;
		window.list_ordenes = $("#list_facturas").DataTable(_table_);

		//Ver detalles
	}




































    /*


        
    if($("#save_data_publico_socio").length){

        const edit_profile = (data)=>{
            loading('Guardando información');
            SendAjax("perfil_socio", 0, {data: JSON.stringify(data)}, ["imagen"]).then(rsp => {
            loading_end();
            if(rsp.r){
                alert(rsp.m, true);
            } else {
                alert(rsp.m);
            }
            }).catch(error => {
            loading_end();
            alert(error);
            });
        };
    
        // Actualiza la vista previa de la imagen y valida extensión
        $("#imagen").change(function(){
            const selectedFile = this.files[0];
            if(selectedFile){
                const fileName = selectedFile.name;
                if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                    $("label[for='imagen'] span").empty();
                    const imgProfile = $(`<img src='${e.target.result}' />`);
                    $("label[for='imagen'] span").append(imgProfile);
                    };
                    reader.readAsDataURL(selectedFile);
                } else {
                    alert('El archivo debe ser una imagen en formato JPG o PNG.');
                }
            }
        });
    
        // Al hacer click en guardar, se arma el objeto con los campos con nombres idénticos a la base de datos
        $("#save_data_publico_socio").click(function(e){
            e.preventDefault();
            const data_socio = {
                nombre_empresa: $("#nombre_empresa").val(),
                nombre_comercial: $("#nombre_comercial").val(),
                sector: $("#sector").val(),
                actividad: $("#actividad").val(),
                numero_empleados: $("#numero_empleados").val(),
                direccion_completa: $("#direccion_completa").val(),
                fecha_constitucion: $("#fecha_constitucion").val(),
                linkedin: $("#linkedin").val(),
                instagram: $("#instagram").val(),
                pass1: "",
                pass2: ""
            };
    
            // Se requieren al menos el nombre, email y teléfono (se puede ajustar según necesidades)
            if(data_socio.nombre_empresa && data_socio.nombre_comercial
                && data_socio.sector && data_socio.actividad
                && data_socio.numero_empleados && data_socio.direccion_completa
                && data_socio.fecha_constitucion
                && data_socio.linkedin && data_socio.instagram
            ){
                edit_profile(data_socio);
            } else {
                alert('Por favor, complete todos los campos requeridos');
            }
        });
    }

    if($("#save_data_privado_socio").length){

        const edit_profile = (data)=>{
            loading('Guardando información');
            SendAjax("perfil_socio", 0, {data: JSON.stringify(data)}, ["imagen"]).then(rsp => {
            loading_end();
            if(rsp.r){
                alert(rsp.m, true);
            } else {
                alert(rsp.m);
            }
            }).catch(error => {
            loading_end();
            alert(error);
            });
        };
    
        // Actualiza la vista previa de la imagen y valida extensión
        $("#imagen").change(function(){
            const selectedFile = this.files[0];
            if(selectedFile){
                const fileName = selectedFile.name;
                if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                    $("label[for='imagen'] span").empty();
                    const imgProfile = $(`<img src='${e.target.result}' />`);
                    $("label[for='imagen'] span").append(imgProfile);
                    };
                    reader.readAsDataURL(selectedFile);
                } else {
                    alert('El archivo debe ser una imagen en formato JPG o PNG.');
                }
            }
        });
    
        // Al hacer click en guardar, se arma el objeto con los campos con nombres idénticos a la base de datos
        $("#save_data_privado_socio").click(function(e){
            e.preventDefault();


            const data_socio = {
                nombre: $("#nombre").val(),
                apellidos: $("#apellidos").val(),
                telefono_movil: $("#telefono_movil").val(),
                telefono_fijo: $("#telefono_fijo").val(),
                pass1: $("#pass1").val(),
                pass2: $("#pass2").val()
            };
    
            // Se requieren al menos el nombre, email y teléfono (se puede ajustar según necesidades)
            if(data_socio.nombre && data_socio.apellidos
                && data_socio.telefono_movil && data_socio.telefono_fijo
            ){
                if(data_socio.pass1 || data_socio.pass2){
                    if(!data_socio.pass1 || !data_socio.pass2){
                        alert('Para cambiar tu contraseña, asegúrate de llenar ambos campos relacionados con la nueva contraseña');
                        return;
                    }
                    if(data_socio.pass1 !== data_socio.pass2){
                        alert('Las contraseñas no son iguales');
                        return;
                    }
                    if(data_socio.pass1.length < 8){
                        alert('Las contraseñas deben tener al menos 8 dígitos');
                        return;
                    }
                    if(!(/[0-9]/.test(data_socio.pass1))){
                        alert('Las contraseñas deben tener al menos 1 número');
                        return;
                    }
                    if(!(/[a-z]/.test(data_socio.pass1))){
                        alert('Las contraseñas deben tener al menos 1 letra minúscula');
                        return;
                    }
                    if(!(/[A-Z]/.test(data_socio.pass1))){
                        alert('Las contraseñas deben tener al menos 1 letra mayúscula');
                        return;
                    }
                    if(!(/[!@#$%^&*(),.?":{}|<>]/.test(data_socio.pass1))){
                        alert('Las contraseñas deben tener al menos 1 carácter especial como !@#$%^&*(),.?":{}|<>');
                        return;
                    }
                }
                edit_profile(data_socio);
            } else {
                alert('Por favor, complete todos los campos requeridos');
            }
        });
    }



    */




});



