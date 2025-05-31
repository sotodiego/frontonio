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
        <ser:ConsultaSRERequest>
            <ser1:Identificacion>
                <NumContrato>{$pickup_data.contract_number|escape:'htmlall':'UTF-8'}</NumContrato>
                <NumDetallable>{$pickup_data.client_number|escape:'htmlall':'UTF-8'}</NumDetallable>
                <CodUsuario>{$pickup_data.CorreosOv2Code|escape:'htmlall':'UTF-8'}</CodUsuario>
                <TipoOperacion>CONSULTA</TipoOperacion>
                <ModoOperacion>{$pickup_data.ModoOperacion|escape:'htmlall':'UTF-8'}</ModoOperacion>
            </ser1:Identificacion>
            <ser1:CriterioConsulta>
                <CodigoSRE>{$pickup_data.CodigoSRE|escape:'htmlall':'UTF-8'}</CodigoSRE>
                <ReferenciaRecogida></ReferenciaRecogida>
            </ser1:CriterioConsulta>
        </ser:ConsultaSRERequest>
    </soapenv:Body>
</soapenv:Envelope>