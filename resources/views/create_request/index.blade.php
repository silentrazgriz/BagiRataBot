<!DOCTYPE html>
<html>
	<head>
		<style>
			ul, li {
				list-style-type: none;
			}
			a {
				margin-right: 5px;
				padding: 5px 10px;
				background: #ccc;
				text-decoration: none;
				color: black;
				border: solid 1px #999;
			}
		</style>
	</head>
	<body>
		<p>Persistent Menu</p>
		<a href="{{ url("chat/postback", "main_menu") }}">Main Menu Postback</a>
		<a href="{{ url("chat/postback", "help") }}">Cancel Action</a>
		<p>Main Menu Quick Reply</p>
		<a href="{{ url("chat/quickreply", ["event_choose/Change_my_current_event"]) }}">Choose Event</a>
		<a href="{{ url("chat/quickreply", ["event_create/Create_new_event"]) }}">New Event</a>
		<a href="{{ url("chat/quickreply", ["event_delete/Forget_an_event"]) }}">Delete Event</a>
		<a href="{{ url("chat/quickreply", ["event_list/See_my_events"]) }}">Show Event</a>
		<p>Event Menu Quick Reply</p>
		<a href="{{ url("chat/quickreply", ["transaction_menu", "Transactions"]) }}">Transaction Menu</a>
		<a href="{{ url("chat/quickreply", ["member_menu", "Members"]) }}">Member Menu</a>
		<a href="{{ url("chat/quickreply", ["event_summary", "Event_Summary"]) }}">Event Summary</a>
		<a href="{{ url("chat/quickreply", ["event_detail", "Detailed_report"]) }}">Event Detail</a>
		<a href="{{ url("chat/quickreply", ["main_menu", "Back_to_main_menu"]) }}">Back to Main Menu</a>
		<p>Transaction Menu Quick Reply</p>
		<a href="{{ url("chat/quickreply", ["transaction_create", "I_have_a_new_transaction"]) }}">Add Transaction</a>
		<a href="{{ url("chat/quickreply", ["transaction_delete", "I_want_to_remove_a_transaction"]) }}">Delete Transaction</a>
		<p>Member Menu Quick Reply</p>
		<a href="{{ url("chat/quickreply", ["member_add", "My_friend_wants_to_join"]) }}">Add Member</a>
		<a href="{{ url("chat/quickreply", ["member_remove", "Someone_is_leaving"]) }}">Delete Member</a>
		<form action="{{ url("chat/message") }}" method="POST">
			<p>
				Message: <input type="text" title="message" name="message"> <input type="submit" value="Send message">
			</p>
		</form>
		<hr>
		<p>
			@foreach (\App\Models\Chat::orderBy('id', 'desc')->take(5)->get() as $key => $chat)
				@if ($key == 0)
					@if(count($chat->quickReplies) > 0)
						@foreach ($chat->quickReplies as $quickReply)
							<a href="{{ url("chat/quickreply", [$quickReply->payload, str_replace(" ", "_", $quickReply->title)]) }}">{{ $quickReply->title }}</a>
						@endforeach
					@endif
					<p>{{ ($key+1) . ". " . $chat->message }}</p>
				@else
					<p>{{ ($key+1) . ". " . $chat->message }}</p>
				@endif
			@endforeach
		</p>
	</body>
</html>