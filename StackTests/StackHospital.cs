 using (var transaction = await _transactionFactory.CreateDbTransactionScopeAsync(token))
    {

        //save basic patient info 
        newPatient = await _patientRepo.CreatePatientAsync(request, transaction, token);

        //save patient medicare information
        if (request.PatientMedicare != null)
            newPatient.PatientMedicare = await _patientRepo.CreatePatientMedicareAsync(newPatient.Id, request.PatientMedicare, transaction, token);

        //save patient flags
        if (request.PatientFlags != null)
            newPatient.PatientFlags = await CreatePatientFlagsAsync(newPatient.Id, request.PatientFlags, transaction, token);

        //save patient code
        if (request.PatientCode != null)
            newPatient.PatientCode = await _patientRepo.CreatePatientCodeAsync(newPatient.Id, request.PatientCode, transaction, token);

        //save patient facilities
        if (request.PatientFacilities != null)
            newPatient.PatientFacilities = await CreatePatientFacilitiesAsync(newPatient.Id, request.PatientFacilities, transaction, token);

       ... etc (this goes on for 15+ subclasses)
If I could avoid having to do this I would but until we are able to re-write more of this ColdFusion front end code this is really the only option.

Is there a pattern or something that would make this a little cleaner? Something like a builder or factory pattern but that handles saving as opposed to creation/instantiation of an object?

This is going to be a common issue I deal with with other domain objects a cleaner way to approach this would be awesome.