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
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://www.correos.es/ServicioPuertaAPuertaBackOffice" xmlns:ser1="http://www.correos.es/ServicioPuertaAPuerta">
<soapenv:Header/>
<soapenv:Body>
  <ser:SolicitudRegistroRecogida>
    <ReferenciaRelacionPaP>1</ReferenciaRelacionPaP>
    <TipoOperacion>ALTA</TipoOperacion>
    <FechaOperacion>{$smarty.now|date_format:"%d-%m-%Y %H:%M:%S"|escape:'html':'UTF-8'}</FechaOperacion>
    <NumContrato>{$pickup_data.contract_number|escape:'htmlall':'UTF-8'}</NumContrato>
    <NumDetallable>{$pickup_data.client_number|escape:'htmlall':'UTF-8'}</NumDetallable>
    <CodSistema></CodSistema>
    <CodUsuario>{$pickup_data.CorreosOv2Code|escape:'htmlall':'UTF-8'}</CodUsuario>
    <ser1:Recogida>
      <ReferenciaRecogida>{$pickup_data.order_reference|escape:'html':'UTF-8'}</ReferenciaRecogida>
      <FecRecogida>{$pickup_data.pickup_date|escape:'html':'UTF-8'}</FecRecogida>
      <HoraRecogida>{$pickup_data.sender_from_time|escape:'html':'UTF-8'}</HoraRecogida>
      <CodAnexo>091</CodAnexo>
      <NomNombreViaRec>{$pickup_data.sender_address|escape:'html':'UTF-8'}</NomNombreViaRec>
      <NomLocalidadRec>{$pickup_data.sender_city|escape:'html':'UTF-8'}</NomLocalidadRec>
      <CodigoPostalRecogida>{$pickup_data.sender_cp|escape:'html':'UTF-8'}</CodigoPostalRecogida>
      <DesPersonaContactoRec>{$pickup_data.sender_name|escape:'html':'UTF-8'}</DesPersonaContactoRec>
      <DesTelefContactoRec>{$pickup_data.sender_phone|escape:'html':'UTF-8'}</DesTelefContactoRec>
      <DesEmailContactoRec>{$pickup_data.sender_email|escape:'html':'UTF-8'}</DesEmailContactoRec>
      <DesObservacionRec>{$pickup_data.observations|escape:'html':'UTF-8'}</DesObservacionRec>
      <NumEnvios>{$pickup_data.bultos|escape:'html':'UTF-8'}</NumEnvios>
      <NumPeso>{$pickup_data.weight|escape:'html':'UTF-8'}</NumPeso>
      <TipoPesoVol>{$pickup_data.type_weight_vol|escape:'html':'UTF-8'}</TipoPesoVol>
      <IndImprimirEtiquetas>{$pickup_data.label_print|escape:'html':'UTF-8'}</IndImprimirEtiquetas>
      <IndDevolverCodSolicitud>S</IndDevolverCodSolicitud>
       {if $pickup_data.label_print == 'S'}
      <ser1:ListaCodEnvios>
      {foreach from=$pickup_data.shipping_numbers item=item}
        <CodigoEnvio>{$item.shipping_number|escape:'html':'UTF-8'}</CodigoEnvio>
      {/foreach}
      </ser1:ListaCodEnvios>
      {/if} 
    </ser1:Recogida>
  </ser:SolicitudRegistroRecogida>
</soapenv:Body>
</soapenv:Envelope>

