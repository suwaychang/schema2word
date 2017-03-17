<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\Auth\SchoolCriterion;

/**
* 
*/
class Tables
{
	/**
	 * 抓出出所有table
	 */
	public function TableShow()
	{
		$tables = DB::select('SHOW TABLES');
		$arr = [];
		foreach ($tables as $table) {
		    foreach ($table as $key => $value){
		        $arr[] =  $value;
		    }
		}
		return $arr;
	}

	/**
	 * 攜出目前目前資料庫的資料表
	 * @return array
	 */
	public function show()
	{
		$table = self::TableShow();
		$query = [];
		$z=0;
		foreach ($table as $item) {
			$tables = DB::select('SHOW TABLE STATUS WHERE name =\'' . $item . '\'');
			$query[$item] = !empty($tables[0]->Comment) ? $tables[0]->Comment : null;
		}

		return $query;
	}
	
	/**
	 * 取出資料表欄位資訊
	 * @param  arryr $table 資料表名稱
	 * @return array
	 */
	public function showFields($table)
	{
		$query = [];
		$z=0;
		foreach ($table as $item) {
			$query[$z]['name'] = $item;
			$tables = DB::select('SHOW TABLE STATUS WHERE name =\'' . $item . '\'');
			$query[$z]['comment'] = !empty($tables[0]->Comment) ? $tables[0]->Comment : null;
			$columns = DB::select('SHOW FULL FIELDS from ' . $item);
			$j = 0;
			foreach ($columns as $value) {
				$query[$z]['fields'][$j]['field'] = $value->Field;
				$query[$z]['fields'][$j]['type'] = $value->Type;
				$query[$z]['fields'][$j]['default'] = $value->Default;
				$query[$z]['fields'][$j]['key'] = $value->Key;
				$query[$z]['fields'][$j]['extra'] = $value->Extra;
				$query[$z]['fields'][$j]['null'] = ($value->Null == 'NO' ? '否' : '是');
				$query[$z]['fields'][$j]['comment'] = !empty($value->Comment)?$value->Comment:null;
				$j++;
			}
			$z++;
		}
		return $query;
	}
	/**
	 * 輸出資料(word)
	 * @param  y 資料表名稱
	 * @return docx
	 */
	public function putWord($table)
	{
		$query = self::showFields($table);
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$phpWord->setDefaultFontSize(11);
		$section = $phpWord->addSection(
		            array('paperSize'=>'A4', 'marginLeft'=>600, 'marginRight'=>600, 'marginTop'=>1100, 'marginBottom'=>1100)
		);
		$header = array('size' => 16, 'bold' => true);
		
		foreach ($query as $k => $v) {
			$section->addText(htmlspecialchars($v['name'], ENT_COMPAT, 'UTF-8'), $header);
			if(!empty($v['comment'])){
				$section->addText(htmlspecialchars('(' . $v['comment'] . ')', ENT_COMPAT, 'UTF-8'));
			}
			// 表格格現
		    $styleTable = [
		    	'borderSize' => 6, 
		    	'borderColor' => '006699', 
		    	'cellMargin' => 80
		    ];
		    // 第一行 標題格式
    		$styleFirstRow = [
    			'borderBottomSize' => 18, 
    			'borderBottomColor' => '0000FF', 
    			'bgColor' => '66BBFF'
    		];
		    // 將表格標題樣式加入
		    $phpWord->addTableStyle('Fancy Table', $styleTable, $styleFirstRow);
		    // 標題垂直對齊
		    $styleCell =[
		    	'valign' => 'center'
		    ];
		    // 標題文字粗體 水平對齊
		    $fontStyle = [
		    	'bold' => true, 
		    	'align' => 'center'
		    ];
			$table = $section->addTable('Fancy Table');
    		$table->addRow(400);
    		$table->addCell(1000, $styleCell)->addText(htmlspecialchars('編號', ENT_COMPAT, 'UTF-8'), $fontStyle);
    		$table->addCell(3200, $styleCell)->addText(htmlspecialchars('欄位名稱', ENT_COMPAT, 'UTF-8'));
            $table->addCell(1400, $styleCell)->addText(htmlspecialchars('型態', ENT_COMPAT, 'UTF-8'));
            $table->addCell(1600, $styleCell)->addText(htmlspecialchars('預設值', ENT_COMPAT, 'UTF-8'));
            $table->addCell(1600, $styleCell)->addText(htmlspecialchars('空值', ENT_COMPAT, 'UTF-8'));
            $table->addCell(1800, $styleCell)->addText(htmlspecialchars('鍵值、附加', ENT_COMPAT, 'UTF-8'));
            $table->addCell(2100, $styleCell)->addText(htmlspecialchars('說明', ENT_COMPAT, 'UTF-8'));
            $i = 1;
            foreach ($v['fields'] as $k1 => $v1) {
				$table->addRow();
                $table->addCell()->addText(htmlspecialchars($i, ENT_COMPAT, 'UTF-8'));
                $table->addCell()->addText(htmlspecialchars($v1['field'], ENT_COMPAT, 'UTF-8'));
                $table->addCell()->addText(htmlspecialchars($v1['type'], ENT_COMPAT, 'UTF-8'));
                $table->addCell()->addText(htmlspecialchars($v1['default'], ENT_COMPAT, 'UTF-8'));
                $table->addCell()->addText(htmlspecialchars($v1['null'], ENT_COMPAT, 'UTF-8'));
 
                // Key & Extra 需要特別處理
                $KeyExtra = $table->addCell();
                $Key = htmlspecialchars($v1['key'], ENT_COMPAT, 'UTF-8');
                $Extra = htmlspecialchars($v1['extra'], ENT_COMPAT, 'UTF-8');
                $KeyExtra->addText($Key);
                $KeyExtra->addText($Extra);
 
                $table->addCell()->addText(htmlspecialchars($v1['comment'], ENT_COMPAT, 'UTF-8'));
 
                $i++; // # 序號 方便和 phpMyAdmin 核對
            }
            // 下一頁 (一個資料表一頁)
            $section->addPageBreak();

		}
		$mime = [
        	'Word2007' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'ODText' => 'application/vnd.oasis.opendocument.text',
			'RTF' => 'application/rtf',
			'HTML' => 'text/html',
			'PDF' => 'application/pdf',
		];
		header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="schema'.date('H:i:s').'.docx"');
        header('Content-Type: '.$mime['Word2007']);
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $filename = 'php://output'; // Change filename to force download
        $phpWord->save($filename);
	}

}