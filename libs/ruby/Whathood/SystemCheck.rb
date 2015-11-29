require 'colorize'

module Whathood

  class SystemCheck                                                                       
                                                                                          
    @_any_failure = false                                                                 
                                                                                          
    BASE_URL = 'http://localhost:80'                                                    
                                                                                          
    def run                                                                               
      check_http_status("/")                                                              
      check_http_status("/api/v1/neighborhood-border/region/Philadelphia")                
      check_http_status("/api/v1/point-election/x/-75.0898801428571/y/40.0218525714286")  
      check_message_queue_worker()                                                        
    end                                                                                   
                                                                                          
    def check_message_queue_worker                                                        
      result = `status wh-worker`                                                         
                                                                                          
      unless result.include? 'running'                                                    
        puts "message queue worker is not running".red                                    
        @_any_failure = true                                                              
      else                                                                                
        puts "message queue worker running".green                                         
      end                                                                                 
      return true                                                                         
    end                                                                                   
                                                                                          
    def check_http_status uri                                                             
      result = `curl -s -o /dev/null -w "%{http_code}" #{BASE_URL}#{uri}`                 
                                                                                          
      unless "200" == result                                                              
        puts "did not get 200 from #{BASE_URL}#{uri}".red                                 
        @_any_failure = true                                                              
        return false                                                                      
      else                                                                                
        puts "status #{uri} OK".green                                                     
      end                                                                                 
      return true                                                                         
    end                                                                                   
                                                                                          
    def any_failure                                                                       
      return @_any_failure                                                                
    end                                                                                   
                                                                                          
  end                          

end
