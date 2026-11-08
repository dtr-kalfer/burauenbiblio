### Adding an SRU Provider

BurauenBiblio supports additional SRU repositories through `./catalog/config/sru_providers.php`. Existing Z39.50 functionality does not need to be replaced.

1. **Obtain the SRU connection details.**

   Request the provider's SRU endpoint, supported SRU version, record schema (preferably MARCXML), available CQL indexes, and any applicable access requirements.

2. **Register the repository in Online Hosts.**

   Navigate to **Admin → Online Hosts** and add a new host entry. Set its service to `SRU` and configure its name and other identifying information.

3. **Take note of the host ID.**

   The Online Hosts list displays the database ID. Use this value as `host_id` in the SRU provider configuration.

4. **Add the provider configuration.**

   Open `/catalog/config/sru_providers.php` and add a provider entry. For example:

   ```json
   {
     "host_id": 30,
     "name": "Example SRU Repository",
     "enabled": true,
     "protocol": "SRU",
     "transport": "CURL",
     "endpoint": "https://example.org/sru",
     "version": "1.2",
     "recordSchema": "marcxml",
     "indexes": {
       "title": "dc.title",
       "author": "dc.creator",
       "subject": "dc.subject",
       "isbn": "bath.isbn",
       "issn": "bath.issn",
       "keyword": "cql.anywhere"
     },
     "relations": {
       "default": "=",
       "title": "=",
       "author": "=",
       "subject": "=",
       "isbn": "=",
       "issn": "=",
       "keyword": "="
     },
     "request": {
       "startRecord": 1,
       "maximumRecords": 10,
       "timeout": 30
     },
		 "capabilities": {
			"supports_explain": true,
			"supports_marcxml": true,
			"supports_isbn": true,
			"supports_issn": true,
			"supports_authority": false,
			"supports_pagination": true
		 }		 
   }
   ```

   Replace the example values with the actual settings supported by the repository. The CQL indexes and relations are provider-specific and should not be assumed to be interchangeable between repositories.

5. **Enable and test the provider.**

   Ensure that the provider is enabled in the JSON configuration and that its `host_id` matches the corresponding Online Hosts database entry. Activate the host and perform test searches using supported fields such as title, author, ISBN, or subject.

**Important notes:**
- Each SRU provider must have a unique `host_id` matching its entry in `lookup_hosts`.
- Do not copy another provider's CQL indexes without verifying compatibility.
- Keep credentials and other sensitive connection details out of publicly distributed configuration files.
- Existing Z39.50 providers can continue using their original host configuration and YAZ search mechanism.
- An SRU provider's endpoint, version, schema, indexes, relations, and request settings are managed through `sru_providers.json`, not the YAZ-specific fields in Online Hosts.
- Only one (1) active online host at a time. (Concurrent multiple host search not supported yet)
