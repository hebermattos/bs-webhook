# -*- encoding : utf-8 -*-
require 'sinatra'
require 'json'

post '/callbacks/boletosimples' do
  verify_signature

  payload = JSON.parse(request_body)
  "Event Code: #{payload['event_code']}"
end

def request_body
  @request_body ||= request.body.read.to_s
end

def secret_key
  ENV['WEBHOOK_SECRET_KEY']
end

def signature_from_request
  request.env['HTTP_X_HUB_SIGNATURE']
end

def generated_signature
  'sha1=' + OpenSSL::HMAC.hexdigest(OpenSSL::Digest.new('sha1'), secret_key, request_body)
end

def verify_signature
  return halt 500, "Signatures didn't match!" unless Rack::Utils.secure_compare(signature_from_request, generated_signature)
end