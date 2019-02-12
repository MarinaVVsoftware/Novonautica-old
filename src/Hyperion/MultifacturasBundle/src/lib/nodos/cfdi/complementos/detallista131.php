<?php

function mf_complemento_detallista131($datos)
{
	// Variable para los namespaces xml
	global $__mf_namespaces__;
	$__mf_namespaces__['detallista']['uri'] = 'http://www.sat.gob.mx/detallista';
	$__mf_namespaces__['detallista']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/detallista/detallista.xsd';

	$atrs = mf_atributos_nodo($datos);
    $xml = "<detallista:detallista type='SimpleInvoiceType' contentVersion='1.3.1' documentStructureVersion='AMC8.1' $atrs>";

    if(isset($datos['requestForPaymentIdentification']))
    {
		$atrs = mf_atributos_nodo($datos['requestForPaymentIdentification']);
		$xml .= "<detallista:requestForPaymentIdentification $atrs/>";
    }
	if(isset($datos['specialInstruction']))
    {
		$atrs = mf_atributos_nodo($datos['requestForPaymentIdentification']);
		$xml .= "<detallista:requestForPaymentIdentification $atrs/>";
		foreach($datos['specialInstruction'] as $idx => $entidad)
		{
			if (isset($entidad['textos']))
			{
				foreach($entidad['textos'] as $idx2 => $subentidad)
				{
					$atrs = mf_atributos_nodo($subentidad);
					$xml .= "<detallista:specialInstruction $atrs ";
				}
			}
			$atrs = mf_atributos_nodo($entidad);
			$xml .= "$atrs/>";
		}
    }
	if(isset($datos['orderIdentification']))
    {
		$atrs = mf_atributos_nodo($datos['orderIdentification']);
		$xml .= "<detallista:orderIdentification $atrs>";
		if(isset($datos['orderIdentification']['referenceIdentification']))
		{
			foreach($datos['orderIdentification']['referenceIdentification']  as $idx2 => $entidad2)
			{
				$atrs = mf_atributos_nodo($entidad2);
				$xml .= "<detallista:referenceIdentification $atrs/>";
			}
		}
		$xml .= "</detallista:orderIdentification>";	
	}
	if(isset($datos['AdditionalInformation']))
	{
		$atrs = mf_atributos_nodo($datos['AdditionalInformation']);
		$xml .= "<detallista:AdditionalInformation $atrs>";
		if(isset($datos['AdditionalInformation']['referenceIdentification']))
		{
			foreach($datos['AdditionalInformation']['referenceIdentification']  as $idx2 => $entidad2)
			{
				$atrs = mf_atributos_nodo($entidad2);
				$xml .= "<detallista:referenceIdentification $atrs/>";
			}
		}
		$xml .= "</detallista:AdditionalInformation>";
	}
	if(isset($datos['DeliveryNote']))
	{
		$xml .= "<detallista:DeliveryNote ";
		if(isset($datos['DeliveryNote']['referenceIdentification']))
		{
			foreach($datos['DeliveryNote']['referenceIdentification']  as $idx2 => $entidad2)
			{
				$atrs = mf_atributos_nodo($entidad2);
				$xml .= "$atrs ";
			}
		}
		$atrsentidad = mf_atributos_nodo($datos['DeliveryNote']);
		$xml .= "$atrsentidad />";
	}
    
	if(isset($datos['buyer']))
    {
		$atrs = mf_atributos_nodo($datos['buyer']);
		$xml .= "<detallista:buyer $atrs>";
		if(isset($datos['buyer']['contactInformation']))
		{
			$atrs = mf_atributos_nodo($datos['buyer']['contactInformation']);
			$xml .= "<detallista:contactInformation $atrs>";
			if(isset($datos['buyer']['contactInformation']['personOrDepartmentName']))
			{
				$atrsentidad = mf_atributos_nodo($datos['buyer']['contactInformation']['personOrDepartmentName']);
				$xml .= "<detallista:personOrDepartmentName $atrsentidad />";
			}
			$xml .= "</detallista:contactInformation>";
		}
		$xml .= "</detallista:buyer>";
	}
	if(isset($datos['seller']))
    {
		$atrs = mf_atributos_nodo($datos['seller']);
		$xml .= "<detallista:seller $atrs>";
		if(isset($datos['seller']['alternatePartyIdentification']))
		{
			$atrsentidad = mf_atributos_nodo($datos['seller']['alternatePartyIdentification']);
			$xml .= "<detallista:alternatePartyIdentification $atrsentidad />";
		}
		$xml .= "</detallista:seller>";
	}
	if(isset($datos['shipTo']))
    {
		$atrs = mf_atributos_nodo($datos['shipTo']);
		$xml .= "<detallista:shipTo $atrs>";
		if(isset($datos['shipTo']['nameAndAddress']))
		{
			foreach($datos['shipTo']['nameAndAddress'] as $idx => $entidad)
			{
				$atrsentidad = mf_atributos_nodo($entidad);
				$xml .= "<detallista:nameAndAddress $atrsentidad />";
			}
		}
		$xml .= "</detallista:shipTo>";
	}
	if(isset($datos['InvoiceCreator']))
    {
		$atrs = mf_atributos_nodo($datos['InvoiceCreator']);
		$xml .= "<detallista:InvoiceCreator $atrs>";
		if(isset($datos['InvoiceCreator']['alternatePartyIdentification']))
		{
			$atrsentidad = mf_atributos_nodo($datos['InvoiceCreator']['alternatePartyIdentification']);
			$xml .= "<detallista:alternatePartyIdentification $atrsentidad />";
		}
		if(isset($datos['InvoiceCreator']['nameAndAddress']))
		{
			$atrsentidad = mf_atributos_nodo($datos['InvoiceCreator']['nameAndAddress']);
			$xml .= "<detallista:nameAndAddress $atrsentidad />";
		}
		$xml .= "</detallista:InvoiceCreator>";
	}
	if(isset($datos['Customs']))
	{
		foreach($datos['Customs'] as $idx2 => $entidad2)
		{
			$xml .= "<detallista:Customs ";
			$atrs = mf_atributos_nodo($entidad2);
			$xml .= "$atrs/> ";
		}
	}
	if(isset($datos['currency']))
    {
		foreach($datos['currency'] as $idx => $entidad)
		{
			$xml .= "<detallista:currency ";
			if(isset($datos['currency'][$idx]['currencyFunction']))
			{
				foreach($datos['currency'][$idx]['currencyFunction'] as $idx2 => $entidad2)
				{
					$atrs = mf_atributos_nodo($entidad2);
					$xml .= "$atrs ";
				}
			}
			$atrs = mf_atributos_nodo($entidad);
			$xml .= "$atrs />";
		}
	}
	if(isset($datos['paymentTerms']))
    {
		$atrs = mf_atributos_nodo($datos['paymentTerms']);
		$xml .= "<detallista:paymentTerms $atrs>";
		if(isset($datos['paymentTerms']['netPayment']))
		{
			$atrs = mf_atributos_nodo($datos['paymentTerms']['netPayment']);
			$xml .= "<detallista:netPayment $atrs>";
			if(isset($datos['paymentTerms']['netPayment']['paymentTimePeriod']))
			{
				$atrs = mf_atributos_nodo($datos['paymentTerms']['netPayment']['paymentTimePeriod']);
				$xml .= "<detallista:paymentTimePeriod $atrs>";
				if(isset($datos['paymentTerms']['netPayment']['paymentTimePeriod']['timePeriodDue']))
				{
					$atrsentidad = mf_atributos_nodo($datos['paymentTerms']['netPayment']['paymentTimePeriod']['timePeriodDue']);
					$xml .= "<detallista:timePeriodDue $atrsentidad />";
				}
				$xml .= "</detallista:paymentTimePeriod>";
			}
			$xml .= "</detallista:netPayment>";
		}
		if(isset($datos['paymentTerms']['discountPayment']))
		{
			$atrsentidad = mf_atributos_nodo($datos['paymentTerms']['discountPayment']);
			$xml .= "<detallista:discountPayment $atrsentidad />";
		}
		$xml .= "</detallista:paymentTerms>";
	}
	if(isset($datos['allowanceCharge']))
    {
		$atrs = mf_atributos_nodo($datos['allowanceCharge']);
		$xml .= "<detallista:allowanceCharge $atrs>";
		if(isset($datos['allowanceCharge']['monetaryAmountOrPercentage']))
		{
			$atrs = mf_atributos_nodo($datos['allowanceCharge']['monetaryAmountOrPercentage']);
			$xml .= "<detallista:monetaryAmountOrPercentage $atrs>";
			if(isset($datos['allowanceCharge']['monetaryAmountOrPercentage']['rate']))
			{
				$atrsentidad = mf_atributos_nodo($datos['allowanceCharge']['monetaryAmountOrPercentage']['rate']);
				$xml .= "<detallista:rate $atrsentidad />";
			}
			$xml .= "</detallista:monetaryAmountOrPercentage>";
		}
		$xml .= "</detallista:allowanceCharge>";
	}
	if(isset($datos['lineItem']))
    {
		foreach($datos['lineItem'] as $idx => $entidad)
		{
			$atrs = mf_atributos_nodo($entidad);
			$xml .= "<detallista:lineItem $atrs>";
			if(isset($entidad['tradeItemIdentification']))
			{
				$atrs = mf_atributos_nodo($entidad['tradeItemIdentification']);
				$xml .= "<detallista:tradeItemIdentification $atrs/>";
			}
			if(isset($entidad['alternateTradeItemIdentification']))
			{
				foreach($entidad['alternateTradeItemIdentification'] as $idx2 => $entidad2)
				{
					$xml .= "<detallista:alternateTradeItemIdentification ";
					$atrs = mf_atributos_nodo($entidad2);
					$xml .= "$atrs/> ";
				}
			}
			if(isset($entidad['tradeItemDescriptionInformation']))
			{
				$atrs = mf_atributos_nodo($entidad['tradeItemDescriptionInformation']);
				$xml .= "<detallista:tradeItemDescriptionInformation $atrs/>";
			}
			if(isset($entidad['invoicedQuantity']))
			{
				$atrs = mf_atributos_nodo($entidad['invoicedQuantity']);
				$xml .= "<detallista:invoicedQuantity $atrs/>";
			}
			if(isset($entidad['aditionalQuantity']))
			{
				foreach($entidad['aditionalQuantity'] as $idx2 => $entidad2)
				{
					$atrs = mf_atributos_nodo($entidad2);
					$xml .= "<detallista:aditionalQuantity $atrs/> ";
				}
			}
			if(isset($entidad['grossPrice']))
			{
				$atrs = mf_atributos_nodo($entidad['grossPrice']);
				$xml .= "<detallista:grossPrice $atrs/>";
			}
			if(isset($entidad['netPrice']))
			{
				$atrs = mf_atributos_nodo($entidad['netPrice']);
				$xml .= "<detallista:netPrice $atrs/>";
			}
			if(isset($entidad['AdditionalInformation']))
			{
				$atrs = mf_atributos_nodo($entidad['AdditionalInformation']);
				$xml .= "<detallista:AdditionalInformation $atrs>";
				if(isset($entidad['AdditionalInformation']['referenceIdentification']))
				{
					$atrs = mf_atributos_nodo($entidad['AdditionalInformation']['referenceIdentification']);
					$xml .= "<detallista:referenceIdentification $atrs/>";
				}
				$xml .= "</detallista:AdditionalInformation>";
			}
			if(isset($entidad['Customs']))
			{
				foreach($entidad['Customs'] as $idx2 => $entidad2)
				{
					$atrs = mf_atributos_nodo($entidad2);
					$xml .= "<detallista:Customs $atrs> ";
					if(isset($entidad2['alternatePartyIdentification']))
					{
						$atrs = mf_atributos_nodo($entidad2['alternatePartyIdentification']);
						$xml .= "<detallista:alternatePartyIdentification $atrs/>";
					}
					$xml .= "</detallista:Customs>";
				}
			}
			if(isset($entidad['LogisticUnits']))
			{
				$atrs = mf_atributos_nodo($entidad['LogisticUnits']);
				$xml .= "<detallista:LogisticUnits $atrs>";
				if(isset($entidad['LogisticUnits']['serialShippingContainerCode']))
				{
					$atrs = mf_atributos_nodo($entidad['LogisticUnits']['serialShippingContainerCode']);
					$xml .= "<detallista:serialShippingContainerCode $atrs/>";
				}
				$xml .= "</detallista:LogisticUnits>";
			}
			if(isset($entidad['palletInformation']))
			{
				$atrs = mf_atributos_nodo($entidad['palletInformation']);
				$xml .= "<detallista:palletInformation $atrs>";
				if(isset($entidad['palletInformation']['description']))
				{
					$atrs = mf_atributos_nodo($entidad['palletInformation']['description']);
					$xml .= "<detallista:description $atrs/>";
				}
				if(isset($entidad['palletInformation']['transport']))
				{
					$atrs = mf_atributos_nodo($entidad['palletInformation']['transport']);
					$xml .= "<detallista:transport $atrs/>";
				}
				$xml .= "</detallista:palletInformation>";
			}
			if(isset($entidad['extendedAttributes']))
			{
				$atrs = mf_atributos_nodo($entidad['extendedAttributes']);
				$xml .= "<detallista:extendedAttributes $atrs>";
				if(isset($entidad['extendedAttributes']['lotNumber']))
				{
					foreach($entidad['extendedAttributes']['lotNumber'] as $idx2 => $entidad2)
					{
						$atrs = mf_atributos_nodo($entidad2);
						$xml .= "<detallista:lotNumber $atrs/> ";
					}
				}
				$xml .= "</detallista:extendedAttributes>";
			}
			if(isset($entidad['allowanceCharge']))
			{
				foreach($entidad['allowanceCharge'] as $idx2 => $entidad2)
				{
					$atrs = mf_atributos_nodo($entidad2);
					$xml .= "<detallista:allowanceCharge $atrs>";
					if(isset($entidad2['monetaryAmountOrPercentage']))
					{
						$atrs = mf_atributos_nodo($entidad2['monetaryAmountOrPercentage']);
						$xml .= "<detallista:monetaryAmountOrPercentage $atrs>";
						if(isset($entidad2['monetaryAmountOrPercentage']['ratePerUnit']))
						{
							$atrsentidad = mf_atributos_nodo($entidad2['monetaryAmountOrPercentage']['ratePerUnit']);
							$xml .= "<detallista:ratePerUnit $atrsentidad />";
						}
						$xml .= "</detallista:monetaryAmountOrPercentage>";
					}
					$xml .= "</detallista:allowanceCharge>";
				}
			}
			if(isset($entidad['tradeItemTaxInformation']))
			{
				foreach($entidad['tradeItemTaxInformation'] as $idx2 => $entidad2)
				{
					$atrs = mf_atributos_nodo($entidad2);
					$xml .= "<detallista:tradeItemTaxInformation $atrs>";
					if(isset($entidad2['tradeItemTaxAmount']))
					{
						$atrs = mf_atributos_nodo($entidad2['tradeItemTaxAmount']);
						$xml .= "<detallista:tradeItemTaxAmount $atrs/>";
					}
					$xml .= "</detallista:tradeItemTaxInformation>";
				}
			}
			if(isset($entidad['totalLineAmount']))
			{
				$atrs = mf_atributos_nodo($entidad['totalLineAmount']);
				$xml .= "<detallista:totalLineAmount $atrs>";
				if(isset($entidad['totalLineAmount']['grossAmount']))
				{
					$atrs = mf_atributos_nodo($entidad['totalLineAmount']['grossAmount']);
					$xml .= "<detallista:grossAmount $atrs/>";
				}
				if(isset($entidad['totalLineAmount']['netAmount']))
				{
					$atrs = mf_atributos_nodo($entidad['totalLineAmount']['netAmount']);
					$xml .= "<detallista:netAmount $atrs/>";
				}
				$xml .= "</detallista:totalLineAmount>";
			}
			$xml .= "</detallista:lineItem>";
		}
    }
	if(isset($datos['totalAmount']))
    {
		$atrs = mf_atributos_nodo($datos['totalAmount']);
		$xml .= "<detallista:totalAmount $atrs/>";
    }
	if(isset($datos['TotalAllowanceCharge']))
    {
		foreach($datos['TotalAllowanceCharge'] as $idx => $entidad)
		{
			if(is_array($datos['TotalAllowanceCharge'][$idx]))
			{
				$atrs = mf_atributos_nodo($datos['TotalAllowanceCharge'][$idx]);
				$xml .= "<detallista:TotalAllowanceCharge $atrs />";
			}
		}
	}
    $xml .= "</detallista:detallista>";
    return $xml;
}