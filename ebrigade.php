<?php
class PDFEB extends FPDI
{
	function Header()
	{
		global $section;
		global $titre;
		global $cisurl, $cisname; // voir config.php
		global $basedir;		
		
		$cursection = array();
		$cursection = $this->FicheSection($section);
		
		$customlocal=$basedir."/images/user-specific/".$cursection['PDF_PAGE'];
		$customdefault=$basedir."/images/user-specific/pdf_page.pdf";
		$generic=$basedir."/fpdf/pdf_page.pdf";
		$fondpdf=((file_exists($customlocal) && $cursection['PDF_PAGE']!="")?$customlocal:(file_exists($customdefault)?$customdefault:$generic));
		
		$pagecount = $this->setSourceFile($fondpdf);		
			
		$this->SetMargins($cursection['PDF_MARGE_TOP'], $cursection['PDF_MARGE_LEFT']);
		$this->SetFont('Arial','',12);
		

		$tplidx = $this->importPage(1);
		$this->useTemplate($tplidx, 0, 0, 210);	
		$adr = "";
		if($cursection['PDF_PAGE']==""){
			$adr = "".$cursection['description']."\n".$cursection['address']." ".$cursection['cp_ville']."\nTél. : ".$cursection['phone']."\nEmail : ".$cursection['email'];
			$this->SetXY(0,20);
		}			
		$this->SetFont('Arial','',10);
		$this->MultiCell(0,4,$adr,0,"R",0);
		$this->SetFont('Arial','',12);
			
		$this->SetXY($this->GetX(),$cursection['PDF_TEXTE_TOP']);
	}
	function Footer()
	{	
		global $section;
		global $titre;
		global $cisurl, $cisname; // voir config.php
		global $basedir;	
		
		$cursection = array();
		$cursection = $this->FicheSection($section);
		
		$customlocal=$basedir."/images/user-specific/".$cursection['PDF_PAGE'];
		$customdefault=$basedir."/images/user-specific/pdf_page.pdf";
		$generic=$basedir."/fpdf/pdf_page.pdf";
		$fondpdf=((file_exists($customlocal) && $cursection['PDF_PAGE']!="")?$customlocal:(file_exists($customdefault)?$customdefault:$generic));
		
		$pagecount = $this->setSourceFile($fondpdf);		
		
		$this->SetTextColor(0,0,0);    
		//Pied de page ../..

		$this->SetY(-$cursection['PDF_TEXTE_BOTTOM']);
		$this->Ln();
		$txt1="";
		$txt2="";	
    // Positionnement à 1,5 cm du bas
    $this->SetXY(170,-25);
    // Police Arial italique 8
    $this->SetFont('Arial','I',8);
    // Numéro et nombre de pages
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}

	function FicheSection($id=0){
		global $cisname;
		$section= array(); 
		$sql = "select s_code, s_description, s_address, s_zip_code, s_phone, s_city, s_email,
		S_PDF_PAGE, S_PDF_MARGE_TOP, S_PDF_MARGE_LEFT, S_PDF_TEXTE_TOP, S_PDF_TEXTE_BOTTOM
		from section 
		where s_id=".intval($id);

		$res = mysql_query($sql);
		while ($row=mysql_fetch_array($res)){
			$section['description']=$row['s_description']; 
			$section['address']=$row['s_address']; 
			$section['cp_ville']=$row['s_zip_code']." ".$row['s_city']; 
			$section['phone']=$row['s_phone']; 
			$section['email']=$row['s_email']; 

			$section['PDF_PAGE'] = (isset($row["S_PDF_PAGE"])?$row["S_PDF_PAGE"]:""); // Le pdf peut avoir 2 pages
			$section['PDF_MARGE_TOP']=(isset($row["S_PDF_MARGE_TOP"])?$row["S_PDF_MARGE_TOP"]:15);
			$section['PDF_MARGE_LEFT']=(isset($row["S_PDF_MARGE_LEFT"])?$row["S_PDF_MARGE_LEFT"]:15);
			$section['PDF_TEXTE_TOP']=(isset($row["S_PDF_TEXTE_TOP"])?$row["S_PDF_TEXTE_TOP"]:40);
			$section['PDF_TEXTE_BOTTOM']=(isset($row["S_PDF_TEXTE_BOTTOM"])?$row["S_PDF_TEXTE_BOTTOM"]:25);
		}
		return $section;
	} // fin FicheSection
	// Tableau simple
	function BasicTable($header, $data)
	{
	    // En-tête
	    foreach($header as $col)
	        $this->Cell(40,7,$col,1);
	    $this->Ln();
	    // Données
	    foreach($data as $row)
	    {
	        foreach($row as $col)
	            $this->Cell(40,6,$col,1);
	        $this->Ln();
	    }
	}	

	// Tableau amélioré
	function ImprovedTable($header, $data)
	{
    	// Largeurs des colonnes
    	$w = array(40, 35, 45, 40);
    	// En-tête
    	for($i=0;$i<count($header);$i++)
     	   $this->Cell($w[$i],7,$header[$i],1,0,'C');
    		$this->Ln();
    	// Données
	    for($p=0;$p<count($data);$p++)    	
    	    $this->Cell($w[$p],6,$data[$p],1,0,'C');
    	    $this->Ln();
    	// Trait de terminaison
    	$this->Cell(array_sum($w),0,'','T');
}

var $B;
var $I;
var $U;
var $HREF;

function PDF($orientation='P',$unit='mm',$format='A4')
{
    //Call parent constructor
    $this->FPDF($orientation,$unit,$format);
    //Initialization
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
}

function WriteHTML($html)
{
    //HTML parser
    $html=str_replace("\n",' ',$html);
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            //Tag
            if($e{0}=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag,$attr)
{
    //Opening tag
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF=$attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';
}

function SetStyle($tag,$enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
        if($this->$s>0)
            $style.=$s;
    $this->SetFont('',$style);
}

function PutLink($URL,$txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}



}; // fin class


?>
