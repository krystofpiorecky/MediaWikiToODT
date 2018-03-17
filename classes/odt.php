<?php

	class ODT
	{
		public static function createFile($name, $source)
		{
			//creates .zip

			$zip = new ZipArchive();
			$filename = "temp.zip";

			if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) 
			{
			    exit("cannot open <$filename>\n");
			}

			//create .xml file into the temp.zip

			$zip->addFromString("content.xml", $source);

			//create META-INF folder with manifest.xml inside

			$zip->addEmptyDir('META-INF');
			$zip->addFromString(
				"META-INF/manifest.xml",
				ODT::manifest()
			);

			echo "file created";
			$zip->close();

			//remanes the temp.zip to .odt with given name

			rename("temp.zip", $name . ".odt");
		}

		public static function createFrom($input)
		{
			//CONVERT code

			$output = $input;

			//loading translate table from translate.json

			$file = file_get_contents("classes/translate.json");
			$table = json_decode($file, true);

			$html = $table['HTML'];
			$mediawiki = $table['MediaWiki'];

			//replace HTML tags 
			foreach($html as $h)
			{
				foreach($h['HTML'] as $htmlTag)
				{
					//OPEN tag
					$output = join(
						explode(
							'<' . $htmlTag . '>', 
							$output
						),
						$h['XMLopen']
					);

					//CLOSE tag
					//works fine with single tag elements, because there is no such thing like </br>

					$output = join(
						explode(
							'</' . $htmlTag . '>', 
							$output
						),
						$h['XMLclose']
					);
				}
			}

			//replace MediaWiki tags 
			foreach($mediawiki as $m)
			{
				$output = ODT::replaceOpenCloseTags(
					$output,
					$m['MediaWiki'], 
					$m['XMLopen'],
					$m['XMLclose']
				);
			}

			$source = ODT::XMLheader() . $output . ODT::XMLfooter();

			ODT::createFile("test", $source);

		}

		public static function replaceOpenCloseTags($string, $tag, $open, $close)
		{
			//$string = string to replace tags in
			//$tag    = MediaWiki tag to replace
			//$open   = opening XML tag
			//$close  = closing XML tag

			$temp = explode($tag, $string);
			$result = '';
			$xmlTags = array($open, $close);
			$xmlIndex = 0;
			foreach($temp as $i => $t)
			{
				if($i > 0)
				{
					$result .= $xmlTags[$xmlIndex];
					$xmlIndex = ($xmlIndex + 1) % 2;
				}

				$result .= $t;
			}

			return $result;
		}

		public static function manifest()
		{
			return '<?xml version="1.0" encoding="UTF-8"?>
				<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
				 	<manifest:file-entry manifest:full-path="/" manifest:media-type="application/vnd.oasis.opendocument.text"/>
				 	<manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>
				</manifest:manifest>';
		}

		public static function XMLheader()
		{
			// header of generated ODT file
			// including styles

			return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
				<office:document-content xmlns:anim="urn:oasis:names:tc:opendocument:xmlns:animation:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0" xmlns:db="urn:oasis:names:tc:opendocument:xmlns:database:1.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:smil="urn:oasis:names:tc:opendocument:xmlns:smil-compatible:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:xlink="http://www.w3.org/1999/xlink" office:version="1.2">
				<office:font-face-decls>
					<style:font-face style:name="Calibri" svg:font-family="Calibri" style:font-family-generic="swiss" style:font-pitch="variable" svg:panose-1="2 15 5 2 2 2 4 3 2 4"/>
					<style:font-face style:name="Times New Roman" svg:font-family="Times New Roman" style:font-family-generic="roman" style:font-pitch="variable" svg:panose-1="2 2 6 3 5 4 5 2 3 4"/>
					<style:font-face style:name="Calibri Light" svg:font-family="Calibri Light" style:font-family-generic="swiss" style:font-pitch="variable" svg:panose-1="2 15 3 2 2 2 4 3 2 4"/>
				</office:font-face-decls>
				<office:automatic-styles>
					' . ODT::XMLstyles() . '
				</office:automatic-styles>
				<office:body>
					<office:text text:use-soft-page-breaks="true">
						<text:p text:style-name="PARAGRAPH">';
		}

		public static function XMLstyles()
		{
			$style =  '
				<style:style 
					style:name="BOLD" 
					style:family="text">
					<style:text-properties 
						fo:font-weight="bold" 
						style:font-weight-asian="bold" 
						style:font-weight-complex="bold"/>
				</style:style>
				<style:style 
					style:name="ITALIC" 
					style:family="text">
					<style:text-properties 
						fo:font-style="italic"/>
				</style:style>
				<style:style 
					style:name="UNDERLINE" 
					style:parent-style-name="Standardnípísmoodstavce" 
					style:family="text">
					<style:text-properties 
						style:text-underline-type="single" 
						style:text-underline-style="solid" 
						style:text-underline-width="auto" 
						style:text-underline-mode="continuous"/>
				</style:style>
				<style:style 
					style:name="STRIKETROUGH" 
					style:parent-style-name="Standardnípísmoodstavce" 
					style:family="text">
					<style:text-properties 
						style:text-line-through-style="solid" 
						style:text-line-through-width="auto" 
						style:text-line-through-color="font-color" 
						style:text-line-through-mode="continuous" 
						style:text-line-through-type="single"/>
				</style:style>
				<style:style 
					style:name="HEADING2" 
					style:family="paragraph">
				    <style:text-properties 
				    	fo:font-size="21pt" 
				    	style:font-size-asian="21pt" 
				    	style:font-size-complex="21pt"/>
				</style:style>
				<style:style 
					style:name="HEADING3" 
					style:family="paragraph">
				    <style:text-properties 
				    	fo:font-size="16.8pt" 
					    fo:font-weight="bold" 
					    style:font-size-asian="16.8pt" 
					    style:font-weight-asian="bold" 
					    style:font-size-complex="16.8pt" 
					    style:font-weight-complex="bold"/>
				</style:style>
			';

			//HEADINGS 4, 5, 6

			for($i = 4; $i < 7; $i++)
			{
				$style .= '
					<style:style 
						style:name="HEADING' . $i .'"  
						style:family="paragraph">
					    <style:text-properties 
					    	fo:font-size="14pt" 
					    	fo:font-weight="bold" 
					    	style:font-size-asian="14pt" 
					    	style:font-weight-asian="bold" 
					    	style:font-size-complex="14pt" 
					    	style:font-weight-complex="bold"/>
					</style:style>
				';
			}

			return $style;
		}

		public static function XMLfooter()
		{
			// footer of generated ODT file

			return '		</text:p>
						</office:text>
					</office:body>
				</office:document-content>';
		}

	}

?>