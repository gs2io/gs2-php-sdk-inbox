<?php
/*
 Copyright Game Server Services, Inc.

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
 */

namespace GS2\Inbox;

use GS2\Core\Gs2Credentials as Gs2Credentials;
use GS2\Core\AbstractGs2Client as AbstractGs2Client;
use GS2\Core\Exception\NullPointerException as NullPointerException;

/**
 * GS2-Inbox クライアント
 *
 * @author Game Server Services, inc. <contact@gs2.io>
 * @copyright Game Server Services, Inc.
 *
 */
class Gs2InboxClient extends AbstractGs2Client {

	public static $ENDPOINT = 'inbox';
	
	/**
	 * コンストラクタ
	 * 
	 * @param string $region リージョン名
	 * @param Gs2Credentials $credentials 認証情報
	 * @param array $options オプション
	 */
	public function __construct($region, Gs2Credentials $credentials, $options = []) {
		parent::__construct($region, $credentials, $options);
	}
	
	/**
	 * 受信ボックスリストを取得
	 * 
	 * @param string $pageToken ページトークン
	 * @param integer $limit 取得件数
	 * @return array
	 * * items
	 * 	* array
	 * 		* inboxId => 受信ボックスID
	 * 		* ownerId => オーナーID
	 * 		* name => 受信ボックス名
	 * 		* serviceClass => サービスクラス
	 * 		* autoDelete => 自動削除設定
	 * 		* cooperationUrl => 連携用URL
	 * 		* createAt => 作成日時
	 * * nextPageToken => 次ページトークン
	 */
	public function describeInbox($pageToken = NULL, $limit = NULL) {
		$query = [];
		if($pageToken) $query['pageToken'] = $pageToken;
		if($limit) $query['limit'] = $limit;
		return $this->doGet(
					'Gs2Inbox', 
					'DescribeInbox', 
					Gs2InboxClient::$ENDPOINT, 
					'/inbox',
					$query);
	}
	
	/**
	 * 受信ボックスを作成<br>
	 * <br>
	 * GS2-Inbox を利用するにはまず受信ボックスを作成します。<br>
	 * 受信ボックスを作成後、受信ボックスにメッセージを送信することでメッセージを届けることができます。<br>
	 * 1つの受信ボックスで、複数のユーザのメッセージを扱うことができますので、ユーザ数分の受信ボックスを作成する必要はありません。<br>
	 * 
	 * @param array $request
	 * * name => 受信ボックス名
	 * * serviceClass => サービスクラス
	 * * autoDelete => 自動削除設定
	 * * cooperationUrl => 連携用URL
	 * @return array
	 * * item
	 * 	* inboxId => 受信ボックスID
	 * 	* ownerId => オーナーID
	 * 	* name => 受信ボックス名
	 * 	* serviceClass => サービスクラス
	 * 	* autoDelete => 自動削除設定
	 * 	* cooperationUrl => 連携用URL
	 * 	* createAt => 作成日時
	 */
	public function createInbox($request) {
		if(is_null($request)) throw new NullPointerException();
		$body = [];
		if(array_key_exists('name', $request)) $body['name'] = $request['name'];
		if(array_key_exists('serviceClass', $request)) $body['serviceClass'] = $request['serviceClass'];
		if(array_key_exists('autoDelete', $request)) $body['autoDelete'] = $request['autoDelete'];
		if(array_key_exists('cooperationUrl', $request)) $body['cooperationUrl'] = $request['cooperationUrl'];
		$query = [];
		return $this->doPost(
					'Gs2Inbox', 
					'CreateInbox', 
					Gs2InboxClient::$ENDPOINT, 
					'/inbox',
					$body,
					$query);
	}

	/**
	 * サービスクラスリストを取得
	 *
	 * @return array サービスクラス
	 */
	public function describeServiceClass() {
		$query = [];
		$result = $this->doGet(
				'Gs2Inbox',
				'DescribeServiceClass',
				Gs2InboxClient::$ENDPOINT,
				'/inbox/serviceClass',
				$query);
		return $result['items'];
	}

	/**
	 * 受信ボックスを取得
	 *
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * @return array
	 * * item
	 * 	* inboxId => 受信ボックスID
	 * 	* ownerId => オーナーID
	 * 	* name => 受信ボックス名
	 * 	* serviceClass => サービスクラス
	 * 	* autoDelete => 自動削除設定
	 * 	* cooperationUrl => 連携用URL
	 * 	* createAt => 作成日時
	 *
	 */
	public function getInbox($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		$query = [];
		return $this->doGet(
				'Gs2Inbox',
				'GetInbox',
				Gs2InboxClient::$ENDPOINT,
				'/inbox/'. $request['inboxName'],
				$query);
	}

	/**
	 * 受信ボックスのステータスを取得
	 *
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * @return array
	 * * status => 状態
	 */
	public function getInboxStatus($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		$query = [];
		return $this->doGet(
				'Gs2Inbox',
				'GetInboxStatus',
				Gs2InboxClient::$ENDPOINT,
				'/inbox/'. $request['inboxName']. '/status',
				$query);
	}

	/**
	 * 受信ボックスを更新
	 *
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * * serviceClass => サービスクラス
	 * * cooperationUrl => 連携用URL
	 * @return array 
	 * * item
	 * 	* inboxId => 受信ボックスID
	 * 	* ownerId => オーナーID
	 * 	* name => 受信ボックス名
	 * 	* serviceClass => サービスクラス
	 * 	* autoDelete => 自動削除設定
	 * 	* cooperationUrl => 連携用URL
	 * 	* createAt => 作成日時
	 */
	public function updateInbox($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		$body = [];
		if(array_key_exists('serviceClass', $request)) $body['serviceClass'] = $request['serviceClass'];
		if(array_key_exists('cooperationUrl', $request)) $body['cooperationUrl'] = $request['cooperationUrl'];
		$query = [];
		return $this->doPut(
				'Gs2Inbox',
				'UpdateInbox',
				Gs2InboxClient::$ENDPOINT,
				'/inbox/'. $request['inboxName'],
				$body,
				$query);
	}
	
	/**
	 * 受信ボックスを削除
	 * 
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 */
	public function deleteInbox($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		$query = [];
		return $this->doDelete(
					'Gs2Inbox', 
					'DeleteInbox', 
					Gs2InboxClient::$ENDPOINT, 
					'/inbox/'. $request['inboxName'],
					$query);
	}

	/**
	 * メッセージを送信<br>
	 * <br>
	 * メッセージを受信ボックスに送信します。<br>
	 * メッセージには本文との他に開封時通知を有効にするかのフラグ、既読状態のフラグといった情報が付加されています。<br>
	 * <br>
	 * 開封時通知を有効にすると、受信ボックスに設定された連携用URLにメッセージIDがPOSTリクエストで通知されます。<br>
	 * メッセージ送信時にも送信したメッセージIDが取得できますので、<br>
	 * 例えば、メッセージに課金用アイテムを添付したい場合は以下の様なメッセージを送信します。<br>
	 * <ul>
	 * <li>送信先: user-0001</li>
	 * <li>メッセージ本文: サーバ障害のお詫びです</li>
	 * <li>開封時通知: 有効</li>
	 * </ul>
	 * このAPIの戻り値に含まれるメッセージIDとユーザID、アイテムの内容(課金用アイテム)をひも付けて保存します。<br>
	 * <br>
	 * その後、ユーザがこのメッセージを開封すると、連携用URLにこのメッセージのメッセージIDが通知されます。<br>
	 * それを受けて、ユーザIDのアカウントにアイテムの内容(課金用アイテム)を付与します。<br>
	 * これで、メッセージにアイテムを添付することができます。<br>
	 * <br>
	 * また、連携用URLを呼び出した際にエラー応答することで、メッセージの開封を失敗させることができます。<br>
	 * これによって、持ち物がいっぱいの場合などにアイテムの付与に失敗しても再度開封処理を実行させることができます。<br>
	 * <br>
	 * 開封時のコールバックは通信障害などの理由により、コールバック先のサーバは正しく処理を行えたけれど、<br>
	 * GS2側のインフラにレスポンスが届かず、結果的に処理が失敗する可能性を考慮する必要があります。<br>
	 * この場合、複数回の開封コールバックが呼び出される可能性がありますので、コールバック処理は冪等性を持った形で実装するようにしてください。<br>
	 *
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * * userId => 宛先ユーザID
	 * * message => メッセージ本文
	 * * cooperation => 開封時に連携用URLを呼び出すか
	 * @return array
	 * * item
	 * 	* messageId => メッセージID
	 * 	* inboxId => 受信ボックスID
	 * 	* userId => 受信ユーザID
	 * 	* message => メッセージ本文
	 * 	* cooperation => 開封時に連携用URLを呼び出すか
	 * 	* date => 受信日時
	 * 	* read => 既読状態
	 */
	public function sendMessage($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		$body = [];
		if(array_key_exists('userId', $request)) $body['userId'] = $request['userId'];
		if(array_key_exists('message', $request)) $body['message'] = $request['message'];
		if(array_key_exists('cooperation', $request)) $body['cooperation'] = $request['cooperation'];
		$query = [];
		return $this->doPost(
				'Gs2Inbox',
				'SendMessage',
				Gs2InboxClient::$ENDPOINT,
				'/inbox/'. $request['inboxName']. '/message',
				$body,
				$query);
	}
	
	/**
	 * メッセージリストを取得<br>
	 * <br>
	 * accessToken には {@link http://static.docs.gs2.io/php/auth/class-GS2.Auth.Gs2AuthClient.html#_login GS2\Auth\Gs2AuthClient::login()} でログインして取得したアクセストークンを指定してください。<br>
	 * 
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * * accessToken => アクセストークン
	 * @param string $pageToken ページトークン
	 * @param integer $limit 取得件数
	 * @return array
	 * * items
	 * 	* array
	 * 		* messageId => メッセージID
	 * 		* inboxId => 受信ボックスID
	 * 		* userId => 受信ユーザID
	 * 		* message => メッセージ本文
	 * 		* cooperation => 開封時に連携用URLを呼び出すか
	 * 		* date => 受信日時
	 * 		* read => 既読状態
	 * * nextPageToken => 次ページトークン
	 */
	public function describeMessage($request, $pageToken = NULL, $limit = NULL) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		if(!array_key_exists('accessToken', $request)) throw new NullPointerException();
		if(is_null($request['accessToken'])) throw new NullPointerException();
		$query = [];
		if($pageToken) $query['pageToken'] = $pageToken;
		if($limit) $query['limit'] = $limit;
		$extparams = [
				'headers' => [
						'X-GS2-ACCESS-TOKEN' => $request['accessToken']
				]
		];
		return $this->doGet(
					'Gs2Inbox', 
					'DescribeMessage', 
					Gs2InboxClient::$ENDPOINT, 
					'/inbox/'. $request['inboxName']. '/message',
					$query,
					$extparams);
	}

	/**
	 * メッセージを取得
	 *
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * * messageId => メッセージID
	 * @return array
	 * * item
	 * 	* messageId => メッセージID
	 * 	* inboxId => 受信ボックスID
	 * 	* userId => 受信ユーザID
	 * 	* message => メッセージ本文
	 * 	* cooperation => 開封時に連携用URLを呼び出すか
	 * 	* date => 受信日時
	 * 	* read => 既読状態
	 *
	 */
	public function getMessage($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		if(!array_key_exists('messageId', $request)) throw new NullPointerException();
		if(is_null($request['messageId'])) throw new NullPointerException();
		$query = [];
		return $this->doGet(
				'Gs2Inbox',
				'GetMessage',
				Gs2InboxClient::$ENDPOINT,
				'/inbox/'. $request['inboxName']. '/message/'. $request['messageId'],
				$query);
	}

	/**
	 * メッセージを開封<br>
	 * <br>
	 * 受信ボックスの設定で開封時自動削除設定が有効な場合は、メッセージは削除されます。<br>
	 * <br>
	 * 連携用URLを呼び出す設定になっている場合、連携用URLにメッセージIDを付与したコールバックが実行されます。<br>
	 * このコールバックをうけて、持ち物を増やしたりすることでメッセージにアイテムを添付することができます。<br>
	 * <br>
	 * レスポンスには連携用URLを呼び出した際の応答内容も含まれますので、開封時にさらにメッセージを表示させるようなこともできます。<br>
	 * 例えば、連携用URLを呼び出した際に「アイテムを手に入れた」というレスポンスを返すことで、このAPIのレスポンスにその文字列も含んだ形で応答されますので、<br>
	 * 開封時にさらにその応答メッセージを使って画面にメッセージとして「アイテムを手に入れた」という表示をすることができます。<br>
	 * <br>
	 * 開封時のコールバックは通信障害などの理由により、コールバック先のサーバは正しく処理を行えたけれど、<br>
	 * GS2側のインフラにレスポンスが届かず、結果的に処理が失敗する可能性を考慮する必要があります。<br>
	 * この場合、複数回の開封コールバックが呼び出される可能性がありますので、コールバック処理は冪等性を持った形で実装するようにしてください。<br>
	 * <br>
	 * accessToken には {@link http://static.docs.gs2.io/php/auth/class-GS2.Auth.Gs2AuthClient.html#_login GS2\Auth\Gs2AuthClient::login()} でログインして取得したアクセストークンを指定してください。<br>
	 * 
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * * messageId => メッセージID
	 * * accessToken => アクセストークン
	 * @return array
	 * * item
	 * 	* messageId => メッセージID
	 * 	* inboxId => 受信ボックスID
	 * 	* userId => 受信ユーザID
	 * 	* message => メッセージ本文
	 * 	* cooperation => 開封時に連携用URLを呼び出すか
	 * 	* date => 受信日時
	 * 	* read => 既読状態
	 *
	 */
	public function readMessage($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		if(!array_key_exists('messageId', $request)) throw new NullPointerException();
		if(is_null($request['messageId'])) throw new NullPointerException();
		if(!array_key_exists('accessToken', $request)) throw new NullPointerException();
		if(is_null($request['accessToken'])) throw new NullPointerException();
		$body = [];
		$query = [];
		$extparams = [
				'headers' => [
						'X-GS2-ACCESS-TOKEN' => $request['accessToken']
				]
		];
		return $this->doPost(
				'Gs2Inbox',
				'ReadMessage',
				Gs2InboxClient::$ENDPOINT,
				'/inbox/'. $request['inboxName']. '/message/'. $request['messageId'],
				$body,
				$query,
				$extparams);
	}
	
	/**
	 * メッセージを削除<br>
	 * <br>
	 * accessToken には {@link http://static.docs.gs2.io/php/auth/class-GS2.Auth.Gs2AuthClient.html#_login GS2\Auth\Gs2AuthClient::login()} でログインして取得したアクセストークンを指定してください。<br>
	 * 
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * * messageId => メッセージID
	 * * accessToken => アクセストークン
	 */
	public function deleteMessage($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		if(!array_key_exists('messageId', $request)) throw new NullPointerException();
		if(is_null($request['messageId'])) throw new NullPointerException();
		if(!array_key_exists('accessToken', $request)) throw new NullPointerException();
		if(is_null($request['accessToken'])) throw new NullPointerException();
		$query = [];
		$extparams = [
				'headers' => [
						'X-GS2-ACCESS-TOKEN' => $request['accessToken']
				]
		];
		return $this->doDelete(
					'Gs2Inbox', 
					'DeleteMessage', 
					Gs2InboxClient::$ENDPOINT, 
					'/inbox/'. $request['inboxName']. '/message/'. $request['messageId'],
					$query,
					$extparams);
	}

	/**
	 * メッセージを複数同時に既読にする。<br>
	 * <br>
	 * 受信ボックスの設定で開封時自動削除設定が有効な場合は、メッセージは削除されます。<br>
	 * <br>
	 * 連携用URLを呼び出す設定になっている場合、連携用URLにメッセージIDを付与したコールバックが実行されます。<br>
	 * このコールバックをうけて、持ち物を増やしたりすることでメッセージにアイテムを添付することができます。<br>
	 * <br>
	 * レスポンスには連携用URLを呼び出した際の応答内容も含まれますので、開封時にさらにメッセージを表示させるようなこともできます。<br>
	 * 例えば、連携用URLを呼び出した際に「アイテムを手に入れた」というレスポンスを返すことで、このAPIのレスポンスにその文字列も含んだ形で応答されますので、<br>
	 * 開封時にさらにその応答メッセージを使って画面にメッセージとして「アイテムを手に入れた」という表示をすることができます。<br>
	 * <br>
	 * 開封時のコールバックは通信障害などの理由により、コールバック先のサーバは正しく処理を行えたけれど、<br>
	 * GS2側のインフラにレスポンスが届かず、結果的に処理が失敗する可能性を考慮する必要があります。<br>
	 * この場合、複数回の開封コールバックが呼び出される可能性がありますので、コールバック処理は冪等性を持った形で実装するようにしてください。<br>
	 * <br>
	 * accessToken には {@link http://static.docs.gs2.io/php/auth/class-GS2.Auth.Gs2AuthClient.html#_login GS2\Auth\Gs2AuthClient::login()} でログインして取得したアクセストークンを指定してください。<br>
	 *
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * * messageId => メッセージID
	 * * accessToken => アクセストークン
	 * @return array
	 * * items
	 * 	* array
	 * 		* messageId => メッセージID
	 * 		* inboxId => 受信ボックスID
	 * 		* userId => 受信ユーザID
	 * 		* message => メッセージ本文
	 * 		* cooperation => 開封時に連携用URLを呼び出すか
	 * 		* date => 受信日時
	 * 		* read => 既読状態
	 * * cooperationResponse => 連携用URLの応答値
	 *
	 */
	public function readMessages($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		if(!array_key_exists('messageIds', $request)) throw new NullPointerException();
		if(is_null($request['messageIds'])) throw new NullPointerException();
		if(!array_key_exists('accessToken', $request)) throw new NullPointerException();
		if(is_null($request['accessToken'])) throw new NullPointerException();
		$body = [];
		$body['messageIds'] = $request['messageIds'];
		if(is_array($body['messageIds'])) $body['messageIds'] = implode(',', $body['messageIds']);
		$query = [];
		$extparams = [
				'headers' => [
						'X-GS2-ACCESS-TOKEN' => $request['accessToken']
				]
		];
		return $this->doPost(
				'Gs2Inbox',
				'ReadMessages',
				Gs2InboxClient::$ENDPOINT,
				'/inbox/'. $request['inboxName']. '/message/multiple',
				$body,
				$query,
				$extparams);
	}

	/**
	 * メッセージを複数同時に削除する。<br>
	 * <br>
	 * accessToken には {@link http://static.docs.gs2.io/php/auth/class-GS2.Auth.Gs2AuthClient.html#_login GS2\Auth\Gs2AuthClient::login()} でログインして取得したアクセストークンを指定してください。<br>
	 *
	 * @param array $request
	 * * inboxName => 受信ボックス名
	 * * messageIds => メッセージIDリスト
	 * * accessToken => アクセストークン
	 */
	public function deleteMessages($request) {
		if(is_null($request)) throw new NullPointerException();
		if(!array_key_exists('inboxName', $request)) throw new NullPointerException();
		if(is_null($request['inboxName'])) throw new NullPointerException();
		if(!array_key_exists('messageIds', $request)) throw new NullPointerException();
		if(is_null($request['messageIds'])) throw new NullPointerException();
		if(!array_key_exists('accessToken', $request)) throw new NullPointerException();
		if(is_null($request['accessToken'])) throw new NullPointerException();
		$query = [];
		$query['messageIds'] = $request['messageIds'];
		if(is_array($query['messageIds'])) $query['messageIds'] = implode(',', $query['messageIds']);
		$extparams = [
				'headers' => [
						'X-GS2-ACCESS-TOKEN' => $request['accessToken']
				]
		];
		return $this->doDelete(
				'Gs2Inbox',
				'DeleteMessages',
				Gs2InboxClient::$ENDPOINT,
				'/inbox/'. $request['inboxName']. '/message/multiple',
				$query,
				$extparams);
	}
	
}