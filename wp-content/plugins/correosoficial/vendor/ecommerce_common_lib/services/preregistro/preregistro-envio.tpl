{**
 * This program is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/.
 *}
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.correos.es/iris6/services/preregistroetiquetas">
<soapenv:Header/>
    <soapenv:Body>
        <PreregistroEnvio>
            <CodEtiquetador>{$shipping_data.CorreosKey|escape:'html':'UTF-8'}</CodEtiquetador>
            <ModDevEtiqueta>2</ModDevEtiqueta>
            <TotalBultos>1</TotalBultos>
            <CanalOrigen>{$shipping_data.source_channel|escape:'html':'UTF-8'}</CanalOrigen>
            <Remitente>
                <Identificacion>
                    {if $shipping_data.carrier_code === 'S0148'}
                    <Nombre>{$shipping_data.sender_name|escape:'html':'UTF-8'}</Nombre>
                    <Apellido1></Apellido1>
                    <Apellido2></Apellido2>
                    {else}
                    <Empresa>{$shipping_data.sender_name|escape:'html':'UTF-8'}</Empresa>
                    <PersonaContacto>{$shipping_data.sender_contact|escape:'html':'UTF-8'}</PersonaContacto>
                    {/if}
                    <Nif>{$shipping_data.sender_nif_cif|escape:'html':'UTF-8'}</Nif> 
                </Identificacion>
                <DatosDireccion>
                    <Direccion>{$shipping_data.sender_address|escape:'html':'UTF-8'}</Direccion>
                    <Localidad>{$shipping_data.sender_city|escape:'html':'UTF-8'}</Localidad>
                </DatosDireccion>
                <CP>{$shipping_data.sender_cp|escape:'html':'UTF-8'}</CP>
                <Telefonocontacto>{$shipping_data.sender_phone|escape:'html':'UTF-8'}</Telefonocontacto>
                <Email>{$shipping_data.sender_email|escape:'html':'UTF-8'}</Email>
                <DatosSMS>
                    <NumeroSMS>{$shipping_data.sender_phone|escape:'html':'UTF-8'}</NumeroSMS>
                    <Idioma>{if $shipping_data.sender_phone}1{/if}</Idioma>
                </DatosSMS>
            </Remitente>
            <Destinatario>
                <Identificacion>
                    {if empty($shipping_data.customer_company)}
                    <Nombre>{$shipping_data.customer_firstname|escape:'html':'UTF-8'}</Nombre>
                    <Apellido1>{$shipping_data.customer_lastname1|escape:'html':'UTF-8'}</Apellido1>
                    <Apellido2>{$shipping_data.customer_lastname2|escape:'html':'UTF-8'}</Apellido2>
                    {else if !empty($shipping_data.customer_company) && $shipping_data.company == 'Correos'}
                    <Empresa>{$shipping_data.customer_company|escape:'html':'UTF-8'}</Empresa>
                    {/if}

                    {if empty($shipping_data.customer_contact)}
                        <PersonaContacto>{$shipping_data.customer_firstname|escape:'html':'UTF-8'} {$shipping_data.customer_lastname1|escape:'html':'UTF-8'} {$shipping_data.customer_lastname2|escape:'html':'UTF-8'}</PersonaContacto>
                    {else}
                        <PersonaContacto>{$shipping_data.customer_contact|escape:'html':'UTF-8'} </PersonaContacto>
                    {/if}
                    <Nif>{$shipping_data.customer_dni|escape:'html':'UTF-8'}</Nif>
                </Identificacion>
                <DatosDireccion>
                    <Direccion>{$shipping_data.delivery_address|escape:'html':'UTF-8'}</Direccion>
                    <Localidad>{$shipping_data.delivery_city|escape:'html':'UTF-8'}</Localidad>
                    <Provincia>{$shipping_data.delivery_state|escape:'html':'UTF-8'}</Provincia>
                </DatosDireccion>
                <CP>{$shipping_data.delivery_postcode|escape:'html':'UTF-8'}</CP>
                <ZIP>{$shipping_data.delivery_zip|escape:'html':'UTF-8'}</ZIP>
                <Pais>{$shipping_data.delivery_country_iso|escape:'html':'UTF-8'}</Pais>
                <Telefonocontacto>{$shipping_data.phone|escape:'html':'UTF-8'}</Telefonocontacto>
                <Email>{$shipping_data.customer_email|escape:'html':'UTF-8'}</Email>
                <DatosSMS>
                    <NumeroSMS>{$shipping_data.phone_mobile_sms|escape:'html':'UTF-8'}</NumeroSMS>
                    <Idioma>{$shipping_data.mobile_lang|escape:'html':'UTF-8'}</Idioma>
                </DatosSMS>
            </Destinatario>
            <Envio>
                <CodProducto>{$shipping_data.carrier_code|escape:'html':'UTF-8'}</CodProducto>
                <ReferenciaCliente>{$shipping_data.order_reference|escape:'html':'UTF-8'}</ReferenciaCliente>
                {if PLATFORM == 'PS'} 
                <ReferenciaCliente3>MODULO_{PLATFORM}_{VERSION}/{CORREOS_OFICIAL_VERSION}</ReferenciaCliente3>
                {/if}
                {if PLATFORM == 'WP'}
                    <ReferenciaCliente3>MODULO_{MODULE}_{VERSION}/{CORREOS_OFICIAL_VERSION}</ReferenciaCliente3>
                {/if}
                <TipoFranqueo>FP</TipoFranqueo>
                <ModalidadEntrega>{$shipping_data.delivery_mode|escape:'html':'UTF-8'}</ModalidadEntrega>
                {if $shipping_data.delivery_mode == 'LS'}<OficinaElegida>{$shipping_data.id_office|escape:'html':'UTF-8'}</OficinaElegida>{/if}
                {if $shipping_data.delivery_mode == 'CP'}<CodigoHomepaq>{$shipping_data.id_citypaq|escape:'html':'UTF-8'}</CodigoHomepaq>{/if}
                <Pesos>
                    <Peso>
                        <TipoPeso>R</TipoPeso>
                        <Valor>{$shipping_data.weight|escape:'html':'UTF-8'}</Valor>
                    </Peso>
                    {if $shipping_data.has_size}
                    <Peso>
                        <TipoPeso>V</TipoPeso>
                        <Valor>{$shipping_data.v_weight|escape:'html':'UTF-8'}</Valor>
                    </Peso>
                    {/if}
                </Pesos>
                {if $shipping_data.has_size}
                <Largo>{$shipping_data.long|escape:'html':'UTF-8'}</Largo>
                <Alto>{$shipping_data.height|escape:'html':'UTF-8'}</Alto>
                <Ancho>{$shipping_data.width|escape:'html':'UTF-8'}</Ancho>
                {/if}
                <ValoresAnadidos>
                    {if $shipping_data.seguro == 1}
                    <ImporteSeguro>{$shipping_data.insurance_value|escape:'html':'UTF-8'}</ImporteSeguro>
                    {/if}
                    {if $shipping_data.contra_reembolso == 1}
                    <Reembolso>
                        <TipoReembolso>{$shipping_data.cashondelivery_type|escape:'html':'UTF-8'}</TipoReembolso>
                        <Importe>{$shipping_data.cashondelivery_value|escape:'html':'UTF-8'}</Importe>
                        <NumeroCuenta>{$shipping_data.cashondelivery_bankac|escape:'html':'UTF-8'}</NumeroCuenta>
                    </Reembolso>
                    {/if}
                    {* <FranjaHorariaConcertada>{$shipping_data.id_schedule|escape:'html':'UTF-8'}</FranjaHorariaConcertada> *}
                    <TextoAdicional>{$shipping_data.texto_adicional|escape:'html':'UTF-8'}</TextoAdicional>
                    <EntregaconRecogida>N</EntregaconRecogida>
                    <IndImprimirEtiqueta>N</IndImprimirEtiqueta>
                </ValoresAnadidos>
                {if $shipping_data.require_customs_doc == 1}
                <Aduana>
                    <TipoEnvio>2</TipoEnvio>
                    <EnvioComercial>S</EnvioComercial>
                    <FacturaSuperiora500>N</FacturaSuperiora500>
                    <DUAConCorreos>N</DUAConCorreos>
                    <RefAduaneraExpedidor>{$shipping_data.customs_consignor_reference|escape:'html':'UTF-8'}</RefAduaneraExpedidor>
                    <DescAduanera>

                        {foreach name=outer item=$descs from=$shipping_data.customs_descs[{$smarty.foreach.shipping.index|intval + 1}] }
                               <DATOSADUANA>
                                   <Cantidad>{$descs['unidades']|escape:'html':'UTF-8'}</Cantidad>
                                   <Descripcion>{$descs['descripcion_aduanera']|escape:'html':'UTF-8'}</Descripcion>
                                   <NTarifario>{$descs['numero_tarifario']|escape:'html':'UTF-8'}</NTarifario>
                                   <Pesoneto>{$descs['weight']|escape:'html':'UTF-8'}</Pesoneto>
                                   <Valorneto>{$descs['valor_neto']|escape:'html':'UTF-8'}</Valorneto>
                               </DATOSADUANA>
                        {/foreach}
                        
                    </DescAduanera>
                </Aduana>
                {/if}
                <Observaciones1>{$shipping_data.observaciones1|escape:'html':'UTF-8'}</Observaciones1>
                <Observaciones2>{$shipping_data.observaciones2|escape:'html':'UTF-8'}</Observaciones2>
                <InstruccionesDevolucion>D</InstruccionesDevolucion>
            </Envio>
        </PreregistroEnvio>
    </soapenv:Body>
</soapenv:Envelope>