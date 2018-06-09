<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\KeywordBlacklist;

class BlacklistKeywordController extends Controller
{
	public function checkMessageContent(Request $request)
	{
		$message = $request->message;
		$blacklisted_keywords = KeywordBlacklist::all()->toArray();


		foreach ($blacklisted_keywords as $bk)
		{
			$needle = $bk['keyword'];

			if (strpos($message, $needle) !== false)
			{
				$data = [
					'status' => -1,
					'message' => $message,
					'error' => 'Message contains blacklisted keyword. Please type again.',
				];

				return response()->json($data);
			}
			else
			{
				$data = [
					'status' => 0,
					'message' => $message,
				];
			}
		}		

		return response()->json($data);
	
	}
}
