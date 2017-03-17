<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Tables;
/**
* 
*/
class HomeController extends Controller
{
	/**
	 * Service
	 * @var null
	 */
	public $AppService = null;

	function __construct(Tables $Service)
	{
		$this->AppService = $Service;
	}
	/**
	 * 首頁
	 * @return view
	 */
	public function index()
	{
		$table = $this->AppService->show();
		return view('home', compact('table','select_table'));
	}
	/**
	 * 輸出選擇的資料表
	 * @param  Request $request 
	 * @return docx
	 */
	public function put(Request $request)
	{
		$select_table = $request->get('table', false);
		if(!empty($select_table)){
			return $this->AppService->putWord($select_table);
		}
	}
}