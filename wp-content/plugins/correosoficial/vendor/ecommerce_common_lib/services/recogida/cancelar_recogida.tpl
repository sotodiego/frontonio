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
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://www.correos.es/ServicioPuertaAPuertaBackOffice">
    <soapenv:Header/>
    <soapenv:Body>
        <ser:AnulacionRecogidaPaPRequest>
            <FechaOperacion>{$smarty.now|date_format:"%d-%m-%Y %H:%M:%S"|escape:'html':'UTF-8'}</FechaOperacion>
            <NumContrato>{$pickup_data.contract_number|escape:'htmlall':'UTF-8'}</NumContrato>
            <NumDetallable>{$pickup_data.client_number|escape:'htmlall':'UTF-8'}</NumDetallable>
            <CodUsuario>{$pickup_data.CorreosOv2Code|escape:'htmlall':'UTF-8'}</CodUsuario>
            <CodSolicitud>{$pickup_data.confirmation_code|escape:'htmlall':'UTF-8'}</CodSolicitud>
            <ReferenciaRecogida>{$pickup_data.confirmation_code|escape:'htmlall':'UTF-8'}</ReferenciaRecogida>
        </ser:AnulacionRecogidaPaPRequest>
    </soapenv:Body>
</soapenv:Envelope>

