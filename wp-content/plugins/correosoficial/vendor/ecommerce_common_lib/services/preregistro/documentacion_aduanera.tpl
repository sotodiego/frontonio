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
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:prer="http://www.correos.es/iris6/services/preregistroetiquetas">
   <soapenv:Header/>
   <soapenv:Body>
      <prer:SolicitudDocumentacionAduanera>
         <!--You may enter the following 12 items in any order-->
         <prer:TipoESAD>{$doc_aduanera_data.optionButton|escape:'html':'UTF-8'}</prer:TipoESAD>
         <prer:NumContrato></prer:NumContrato>
         <prer:NumCliente></prer:NumCliente>
         <prer:CodEtiquetador>{$doc_aduanera_data.cod_etiquetador|escape:'html':'UTF-8'}</prer:CodEtiquetador>
         <prer:Provincia></prer:Provincia>
         <prer:PaisDestino>{$doc_aduanera_data.customer_country|escape:'html':'UTF-8'}</prer:PaisDestino>
         <prer:NombreDestinatario>{$doc_aduanera_data.customer_name|escape:'html':'UTF-8'}</prer:NombreDestinatario>
         <prer:NumeroEnvios>1</prer:NumeroEnvios>
         <!--Optional:-->
         <prer:LocalidadFirma></prer:LocalidadFirma>
         <!--Optional:-->
         <prer:FechaFirma></prer:FechaFirma>
         <!--Optional:-->
         <prer:NifFirma></prer:NifFirma>
         <!--Optional:-->
         <prer:NombreFirma></prer:NombreFirma>
      </prer:SolicitudDocumentacionAduanera>
   </soapenv:Body>
</soapenv:Envelope>
