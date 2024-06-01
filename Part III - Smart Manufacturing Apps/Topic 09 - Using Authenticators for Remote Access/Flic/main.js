// main.js
var buttonManager = require("buttons");
var http = require("http");
var token = null;
var attrId = 0;

const authenticator = {
	"graphQlEndpoint": "https://services.dev.thinkiq.net/graphql",
	"clientId": "ThinkIQ.GraphQL.UptimeService",
	"clientSecret": "2a320cfc-5181-4029-8de2-3b7174bbe945",
	"role": "services_ro_group",
	"userName": "ThinkIQ.GraphQL.UptimeService",
};

const authRequestQuery = 'mutation authRequest {authenticationRequest(input: {authenticator: "' + authenticator.clientId + '", role: "' + authenticator.role + '", userName: "' + authenticator.userName +'"}) {jwtRequest {challenge message}}}';
//console.log(authRequestQuery);

function getAuthValidationQuery(challenge, authenticator){
    var query = 'mutation authValidation {authenticationValidation(input: {authenticator: "' + authenticator.clientId + '", signedChallenge: "' + challenge + '|' + authenticator.clientSecret + '"}) {jwtClaim}}';
		return query;
}

const getReceiverQuery = 'query q1 {tiqTypes(condition: { displayName: "FlicReceiver" }) { id objectsByTypeId { id displayName attributes { id displayName }}}}';

function getSendMessageQuery(aPayload){
	var query = 'mutation m1 { replaceTimeSeriesRange( input: { attributeOrTagId: "' + attrId + '" entries: [{ value: ' + JSON.stringify(JSON.stringify(aPayload)) + ', timestamp: "' + (new Date()).toISOString() + '", status: "0" }]}) {clientMutationId json}}';
	//console.log(query);
	return query;
}

function isTokenExpired() {
	//console.log("token:", token);
	if(token==null) return true;
  const arrayToken = token.split('.');
	const part2 = JSON.parse(new TextDecoder().decode(Duktape.dec('base64', arrayToken[1])));
  return Math.floor(new Date().getTime() / 1000000) >= part2.sub;
}

function withValidToken(callback){
	if(!isTokenExpired(token)){
		callback()
	}else{
		http.makeRequest({
			url: authenticator.graphQlEndpoint,
			method: "POST",
			headers: {"Content-Type": "application/json"},
			content: JSON.stringify({query:authRequestQuery}),
		}, function(err, res) {
			var content = JSON.parse(res.content);
			var challenge = content.data.authenticationRequest.jwtRequest.challenge;
			//console.log(challenge);

			http.makeRequest({
				url: authenticator.graphQlEndpoint,
				method: "POST",
				headers: {"Content-Type": "application/json"},
				content: JSON.stringify({query:getAuthValidationQuery(challenge, authenticator)}),
			}, function(err, res) {
				var content = JSON.parse(res.content);
				token = content.data.authenticationValidation.jwtClaim;
				//console.log(token);
				//console.log(isTokenExpired(token));
				
				callback()
			})
		})
	}
}

function withAttributeID(callback){
	if(attrId != 0){
		callback()
	}else{
		withValidToken(function() {
			http.makeRequest({
				url: authenticator.graphQlEndpoint,
				method: "POST",
				headers: {"Content-Type": "application/json", "Authorization": "Bearer " + token},
				content: JSON.stringify({query:getReceiverQuery}),
			}, function(err, res) {
				var content = JSON.parse(res.content);
				attrId = content.data.tiqTypes[0].objectsByTypeId[0].attributes[0].id;
				//console.log(JSON.stringify(attrId));
				
				callback()
			})
		})
	}
}

function withTokenAndAttributeID(callback){
	withValidToken(function(){
		withAttributeID(callback)
	})
}

buttonManager.on("buttonSingleOrDoubleClickOrHold", function(obj) {
	//console.log(JSON.stringify(obj));
	var button = buttonManager.getButton(obj.bdaddr);
	obj.button = button;
	//console.log(JSON.stringify(button));
	var clickType = obj.isSingleClick ? "click" : obj.isDoubleClick ? "double_click" : "hold";
	
	withTokenAndAttributeID(function(){
		http.makeRequest({
			url: authenticator.graphQlEndpoint,
			method: "POST",
			headers: {"Content-Type": "application/json", "Authorization": "Bearer " + token},
			content: JSON.stringify({query:getSendMessageQuery(obj)}),
		}, function(err, res) {
			var content = JSON.parse(res.content);
			data = content.data;
			//console.log(JSON.stringify(data));



		});
	})		
});
