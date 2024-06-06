// main.js
var buttonManager = require("buttons");
var http = require("http");
var token = "";
var attrId = 0;
var expEpoch = 0;

const authenticator = {
	"graphQlEndpoint": "https://xxx.xxx.thinkiq.net/graphql",
	"clientId": "ThinkIQ.GraphQL.xxx",
	"clientSecret": "xxx-xxx-xxx-xxx-xxx",
	"role": "xxx_ro_group",
	"userName": "ThinkIQ.GraphQL.xxx",
};

const authRequestQuery = 'mutation authRequest {authenticationRequest(input: {authenticator: "' + authenticator.clientId + '", role: "' + authenticator.role + '", userName: "' + authenticator.userName + '"}) {jwtRequest {challenge message}}}';
//console.log(authRequestQuery);

function getAuthValidationQuery(challenge, authenticator) {
	var query = 'mutation authValidation {authenticationValidation(input: {authenticator: "' + authenticator.clientId + '", signedChallenge: "' + challenge + '|' + authenticator.clientSecret + '"}) {jwtClaim}}';
	return query;
}

const getReceiverQuery = 'query q1 {tiqTypes(condition: { displayName: "FlicReceiver" }) { id objectsByTypeId { id displayName attributes { id displayName }}}}';

function getTargetAttrIdQuery(obj) {
	var attrName = obj.isSingleClick ? 'Single Click Target' : obj.isDoubleClick ? 'Double Click Target' : 'Click Hold Target';
	//console.log(attrName);
	query = 'query q2 { attributes( filter: { displayName: { equalTo: "mac address" } and: { stringValue: { equalTo: "' + obj.bdaddr + '" } } } ) { onObject { attributes(condition: { displayName: "' + attrName + '" }) { referencedAttribute { id }}}}}';
	//console.log(query);
	return query;
}

function getSendMessageQuery(aPayload) {
	var query = '';
	if (targetAttrId == 0) {
		query = 'mutation m1 { replaceTimeSeriesRange( input: { attributeOrTagId: "' + messagesAttrId + '" entries: [{ value: ' + JSON.stringify(JSON.stringify(aPayload)) + ', timestamp: "' + (new Date()).toISOString() + '", status: "0" }]}) {clientMutationId json}}';
	} else {
		query = 'mutation m1 { m1: replaceTimeSeriesRange( input: { attributeOrTagId: "' + messagesAttrId + '" entries: [{ value: ' + JSON.stringify(JSON.stringify(aPayload)) + ', timestamp: "' + (new Date()).toISOString() + '", status: "0" }]}) {clientMutationId json} m2: replaceTimeSeriesRange( input: { attributeOrTagId: "' + targetAttrId + '" entries: [{ value: "1", timestamp: "' + (new Date()).toISOString() + '", status: "0" }]}) {clientMutationId json}}';
	}
	//console.log(query);
	return query;
}

function isTokenValid() {
	//console.log("isTokenExpired");
	if (token == "") return false;

	//console.log(Math.floor(new Date().getTime() / 1000), expEpoch);
	return Math.floor(new Date().getTime() / 1000) < expEpoch;
}

function withValidToken(callback) {
	//console.log("withValidToken");
	if (isTokenValid(token)) {
		callback()
	} else {
		http.makeRequest({
			url: authenticator.graphQlEndpoint,
			method: "POST",
			headers: { "Content-Type": "application/json" },
			content: JSON.stringify({ query: authRequestQuery }),
		}, function (err, res) {
			var content = JSON.parse(res.content);
			var challenge = content.data.authenticationRequest.jwtRequest.challenge;
			//console.log(challenge);

			http.makeRequest({
				url: authenticator.graphQlEndpoint,
				method: "POST",
				headers: { "Content-Type": "application/json" },
				content: JSON.stringify({ query: getAuthValidationQuery(challenge, authenticator) }),
			}, function (err, res) {
				var content = JSON.parse(res.content);
				token = content.data.authenticationValidation.jwtClaim;
				const arrayToken = token.split('.');
				expEpoch = JSON.parse(new TextDecoder().decode(Duktape.dec('base64', arrayToken[1]))).exp;

				callback()
			})
		})
	}
}

function withAttributeID(callback) {
	//console.log("withAttributeID");
	if (messagesAttrId != 0) {
		callback()
	} else {
		withValidToken(function () {
			http.makeRequest({
				url: authenticator.graphQlEndpoint,
				method: "POST",
				headers: { "Content-Type": "application/json", "Authorization": "Bearer " + token },
				content: JSON.stringify({ query: getReceiverQuery }),
			}, function (err, res) {
				var content = JSON.parse(res.content);
				messagesAttrId = content.data.tiqTypes[0].objectsByTypeId[0].attributes[0].id;
				//console.log(JSON.stringify(messagesAttrId));

				callback()
			})
		})
	}
}

function withTokenAndAttributeID(callback) {
	//console.log("withTokenAndAttributeID");
	withValidToken(function () {
		withAttributeID(callback)
	})
}

function postMessage(obj) {
	//console.log("postMessage");
	withTokenAndAttributeID(function () {
		http.makeRequest({
			url: authenticator.graphQlEndpoint,
			method: "POST",
			headers: { "Content-Type": "application/json", "Authorization": "Bearer " + token },
			content: JSON.stringify({ query: getTargetAttrIdQuery(obj) }),
		}, function (err, res) {
			var content = JSON.parse(res.content);
			data = content.data;
			//console.log(JSON.stringify(data));
			targetAttrId = 0;
			if (data.attributes.length > 0) {
				if (data.attributes[0].onObject.attributes.length > 0) {
					if (data.attributes[0].onObject.attributes[0].referencedAttribute != null) {
						targetAttrId = data.attributes[0].onObject.attributes[0].referencedAttribute.id;
					}
				}
			}
			//console.log(JSON.stringify(obj));
			http.makeRequest({
				url: authenticator.graphQlEndpoint,
				method: "POST",
				headers: { "Content-Type": "application/json", "Authorization": "Bearer " + token },
				content: JSON.stringify({ query: getSendMessageQuery(obj) }),
			}, function (err, res) {
				var content = JSON.parse(res.content);
				data = content.data;
				//console.log(JSON.stringify(data));
			})
		})
	})
}

buttonManager.on("buttonSingleOrDoubleClickOrHold", function (obj) {
	//console.log(JSON.stringify(obj));
	var button = buttonManager.getButton(obj.bdaddr);
	obj.button = button;
	//message = obj;
	//console.log(JSON.stringify(button));
	var clickType = obj.isSingleClick ? "click" : obj.isDoubleClick ? "double_click" : "hold";

	postMessage(obj);

});

