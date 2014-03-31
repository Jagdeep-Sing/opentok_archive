require 'json'
require 'rest_client'

InvalidInput = "Your Id input for Customer or Representative uses invalid characters or data length exceeds 8000 characters"
InvalidKeySecret = "Invalid API Key or Secret"
Conflict = "Could not perform action requested"

class ArchivingBeta
  attr_accessor :apiKey, :secret, :server

  def initialize( *args )
    @apiKey, @secret = args
    if @apiKey.nil? or @secret.nil? then raise "Please put in your OpenTok API_KEY and API_SECRET" end
    @server = "https://api.opentok.com/"
  end

  def getURL(url)
    full_url = "#{@server}v2/partner/#{@apiKey}#{url}"
    opts = { 'X-TB-PARTNER-AUTH' => "#{@apiKey}:#{@secret}" }
    RestClient.get(full_url, opts) { |response, request, result, &block|
      case response.code
      when 403
        raise InvalidKeySecret
      else
        if response.length > 2
          yield JSON.parse(response), response.code
        else
          yield nil, response.code
        end
      end
    }
  end

  def putURL(url, body)
    full_url = "#{@server}v2/partner/#{@apiKey}#{url}"
    opts = {  content_type: :json,  'X-TB-PARTNER-AUTH' => "#{@apiKey}:#{@secret}" }
    RestClient.post(full_url, body.to_json, opts) { |response, request, result, &block|
      case response.code
      when 403
        raise InvalidKeySecret
      else
        if response.length > 2
          yield JSON.parse(response), response.code
        else
          yield nil, response.code
        end
      end
    }
  end

  def postURL(url, body)
    full_url = "#{@server}v2/partner/#{@apiKey}#{url}"
    opts = {  content_type: :json,  'X-TB-PARTNER-AUTH' => "#{@apiKey}:#{@secret}" }
    RestClient.post(full_url, body.to_json, opts) { |response, request, result, &block|
      case response.code
      when 403
        raise InvalidKeySecret
      else
        if response.length > 2
          yield JSON.parse(response), response.code
        else
          yield nil, response.code
        end
      end
    }
  end

  def deleteURL(url)
    full_url = "#{@server}v2/partner/#{@apiKey}#{url}"
    opts = { 'X-TB-PARTNER-AUTH' => "#{@apiKey}:#{@secret}" }
    RestClient.delete(full_url, opts) { |response, request, result, &block|
      case response.code
      when 403
        raise InvalidKeySecret
      else
        if response.length > 2
          yield JSON.parse(response), response.code
        else
          yield nil, response.code
        end
      end
    }
  end

  def start(session_id, archive_name)
    postURL("/archive", :action => :start, :sessionId => session_id, :name => archive_name) do |response, code|
      unless code == 200 or code == 201
        raise "Start archiving error: " + (response && response.message || "Unknown error")
      else
        return response
      end
    end
  end

  def stop(archive_id)
    putURL("/archive/#{archive_id}", :action => :stop) do |response, code|
      unless code == 200 or code == 201
        raise "Stop archiving error: " + (response && response.message || "Unknown error")
      else
        return response
      end
    end
  end

  def delete(archive_id)
    deleteURL("/archive/#{archive_id}") do |response, code|
      unless code == 204
        raise "Delete archive error: " + (response && response.message || "Unknown error")
      else
        return true
      end
    end
  end

  def get(archive_id)
    getURL("/archive/#{archive_id}") do |response, code|
      unless code == 200 or code == 201
        raise "Get archive error: " + (response && response.message || "Unknown error")
      else
        return response
      end
    end
  end

  def list(offset = 0, count = 10)
    getURL("/archive?offset=#{offset}&count=#{count}") do |response, code|
      unless code == 200 or code == 201
        raise "List archives error: " + (response && response.message || "Unknown error")
      else
        return response
      end
    end
  end

end
