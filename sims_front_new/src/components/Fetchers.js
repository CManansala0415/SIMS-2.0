import axios from 'axios';

let applicant = {}
const getApplicant = async (limit, offset, fname, mname, lname, mode) => {
    let firstname = !fname ? '404' : fname
    let middlename = !mname ? '404' : mname
    let lastname = !lname ? '404' : lname
    try {
        await axios({
            method: "GET",
            url: 'api/get-applicant/' + limit + '/' + offset + '/' + firstname + '/' + middlename + '/' + lastname + '/' + mode,
        }).then(async (results) => {
            // console.log(results.data)
            applicant = results.data
        })
        return applicant
    } catch (err) {
        return err
    }
}

let person = {}
const getPerson = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-person/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            person = results.data
        })
        return person
    } catch (err) {
        return err
    }
}

let family = {}
const getFamily = async (id, mode) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-family/' + id + '/' + mode,
        }).then(async (results) => {
            // console.log(results.data)
            family = results.data
        })
        return family
    } catch (err) {
        return err
    }
}

let award = {}
const getAward = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-award/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            award = results.data
        })
        return award
    } catch (err) {
        return err
    }
}

let attainment = {}
const getAttainment = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-attainment/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            attainment = results.data
        })
        return attainment
    } catch (err) {
        return err
    }
}

let gender = {}
const getGender = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-gender/',
        }).then(async (results) => {
            // console.log(results.data)
            gender = results.data
        })
        return gender
    } catch (err) {
        return err
    }
}

let nationality = {}
const getNationality = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-nationality/',
        }).then(async (results) => {
            // console.log(results.data)
            nationality = results.data
        })
        return nationality
    } catch (err) {
        return err
    }
}

let civilstatus = {}
const getCivilStatus = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-civilstatus/',
        }).then(async (results) => {
            // console.log(results.data)
            civilstatus = results.data
        })
        return civilstatus
    } catch (err) {
        return err
    }
}

let region = {}
const getRegion = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-region/',
        }).then(async (results) => {
            // console.log(results.data)
            region = results.data
        })
        return region
    } catch (err) {
        return err
    }
}

let demograph = {}
const getDemograph = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-demograph/',
        }).then(async (results) => {
            // console.log(results.data)
            demograph = results.data
        })
        return demograph
    } catch (err) {
        return err
    }
}

let academicdefaults = {}
const getAcademicDefaults = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-academicdefaults/',
        }).then(async (results) => {
            // console.log(results.data)
            academicdefaults = results.data
        })
        return academicdefaults
    } catch (err) {
        return err
    }
}

let country = {}
const getCountry = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-country/',
        }).then(async (results) => {
            // console.log(results.data)
            country = results.data
        })
        return country
    } catch (err) {
        return err
    }
}

let province = {}
const getProvince = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-province/',
        }).then(async (results) => {
            // console.log(results.data)
            province = results.data
        })
        return province
    } catch (err) {
        return err
    }
}

let city = {}
const getCity = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-city/',
        }).then(async (results) => {
            // console.log(results.data)
            city = results.data
        })
        return city
    } catch (err) {
        return err
    }
}

let barangay = {}
const getBarangay = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-barangay/',
        }).then(async (results) => {
            // console.log(results.data)
            barangay = results.data
        })
        return barangay
    } catch (err) {
        return err
    }
}

let gradelvl = {}
const getGradelvl = async (type) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-gradelvl/',
        }).then(async (results) => {
            // console.log(results.data)
            gradelvl = results.data
        })
        return gradelvl
    } catch (err) {
        return 500
    }
}

let program = {}
const getProgram = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-program/',
        }).then(async (results) => {
            // console.log(results.data)
            program = results.data
        })
        return program
    } catch (err) {
        return err
    }
}

let specialization = {}
const getSpecialization = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-specialization/',
        }).then(async (results) => {
            // console.log(results.data)
            specialization = results.data
        })
        return specialization
    } catch (err) {
        return err
    }
}

let quarter = {}
const getQuarter = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-quarter/',
        }).then(async (results) => {
            // console.log(results.data)
            quarter = results.data
        })
        return quarter
    } catch (err) {
        return err
    }
}

let semester = {}
const getSemester = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-semester/',
        }).then(async (results) => {
            // console.log(results.data)
            semester = results.data
        })
        return semester
    } catch (err) {
        return err
    }
}

let section = {}
const getSection = async (type) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-section/',
        }).then(async (results) => {
            // console.log(results.data)
            section = results.data
        })
        return section
    } catch (err) {
        return 500
    }
}

let degree = {}
const getDegree = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-degree/',
        }).then(async (results) => {
            degree = results.data
        })
        return degree
    } catch (err) {
        return err
    }
}

let programlist = {}
const getProgramList = async () => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-program-list/',
        }).then(async (results) => {
            // console.log(results.data)
            programlist = results.data
        })
        return programlist
    } catch (err) {
        return err
    }
}



let response = {}
const addApplicant = async (data, type) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-applicant/' + type,
            data: data

        }).then(async (results) => {
            response = results.data
        })
        return response
    } catch (err) {
        return 500
    }
}

let updateResponse = {}
const updateApplicant = async (data, type) => {
    try {
        if (type == 1) {// 1 means update, 2 means delete
            await axios({
                method: "POST",
                url: 'api/update-applicant/',
                data: data

            }).then(async (results) => {
                updateResponse = results.data
            })
        } else {
            await axios({
                method: "POST",
                url: 'api/delete-applicant/',
                data: data

            }).then(async (results) => {
                updateResponse = results.data
            })
        }
        return updateResponse
    } catch (err) {
        return 500
    }

}

let detailsresponse = {}
const updatePersonDetails = async (data, type) => {
    try {
        await axios({
            method: "POST",
            url: 'api/update-perdetails/' + type,
            data: data

        }).then(async (results) => {
            detailsresponse = results.data
        })
        return detailsresponse
    } catch (err) {
        return 500
    }

}

let deleteresponse = {}
const deleteFamAwrAtt = async (data, type) => {
    try {
        await axios({
            method: "POST",
            url: 'api/delete-perdetails/' + type,
            data: data

        }).then(async (results) => {
            deleteresponse = results.data
        })
        return deleteresponse
    } catch (err) {
        return 500
    }

}

let enroll = {}
const enrollApplicant = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/enroll-applicant/',
            data: data

        }).then(async (results) => {
            enroll = results.data
        })
        return enroll
    } catch (err) {
        return 500
    }
}

let enrollment = {}
const getEnrollment = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-enrollment/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            enrollment = results.data
        })
        return enrollment
    } catch (err) {
        return err
    }
}

let student = {}
const getStudent = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-student/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            student = results.data
        })
        return student
    } catch (err) {
        return err
    }
}

let studentfiltering = {}
const getStudentFiltering = async (limit, offset, fname, mname, lname, program, gradelvl, course, mode) => {
    let firstname = !fname ? '404' : fname
    let middlename = !mname ? '404' : mname
    let lastname = !lname ? '404' : lname
    try {
        await axios({
            method: "GET",
            url: 'api/get-student-filtering/' + limit + '/' + offset + '/' + firstname + '/' + middlename + '/' + lastname + '/' + program + '/' + gradelvl + '/' + course + '/' + mode,
        }).then(async (results) => {
            // console.log(results.data)
            studentfiltering = results.data
        })
        return studentfiltering
    } catch (err) {
        return err
    }
}

let studentiddetails = {}
const getStudentIdDetails = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-student-id-details/' + id
        }).then(async (results) => {
            // console.log(results.data)
            studentiddetails = results.data
        })
        return studentiddetails
    } catch (err) {
        return err
    }
}

let studentbycourse = {}
const getStudentByCourse = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-student-by-course/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            studentbycourse = results.data
        })
        return studentbycourse
    } catch (err) {
        return err
    }
}

let uploadprofile = {}
const uploadProfile = async (data, old, folder) => {
    try {
        await axios({
            method: "POST",
            url: 'api/upload-profile/' + old + '/' + folder,
            data: data

        }).then(async (results) => {
            uploadprofile = results.data
        })
        return uploadprofile
    } catch (err) {
        return 500
    }
}

let uploadlinkprofile = {}
const uploadLinkProfile = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/upload-link-profile/',
            data: data

        }).then(async (results) => {
            uploadlinkprofile = results.data
        })
        return uploadlinkprofile
    } catch (err) {
        return 500
    }
}

let uploadsignature = {}
const uploadSignature = async (data, old) => {
    try {
        await axios({
            method: "POST",
            url: 'api/upload-signature/' + old,
            data: data

        }).then(async (results) => {
            uploadsignature = results.data
        })
        return uploadsignature
    } catch (err) {
        return 500
    }
}

let uploadlinksignature = {}
const uploadLinkSignature = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/upload-link-signature/',
            data: data

        }).then(async (results) => {
            uploadlinksignature = results.data
        })
        return uploadlinksignature
    } catch (err) {
        return 500
    }
}

let curriculum = {}
const getCurriculum = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-curriculum/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            curriculum = results.data
        })
        return curriculum
    } catch (err) {
        return 500
    }
}


let curriculumsett = {}
const getCurriculumSett = async () => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-curriculum-sett',
        }).then(async (results) => {
            // console.log(results.data)
            curriculumsett = results.data
        })
        return curriculumsett
    } catch (err) {
        return 500
    }
}

let curriculumstudent = {}
const getCurriculumStudent = async (prog, type) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-curriculum-student/' + prog + '/' + type,
        }).then(async (results) => {
            // console.log(results.data)
            curriculumstudent = results.data
        })
        return curriculumstudent
    } catch (err) {
        return 500
    }
}

let taggedsubject = {}
const getTaggedSubject = async (type) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-tagged-subject/',
        }).then(async (results) => {
            // console.log(results.data)
            taggedsubject = results.data
        })
        return taggedsubject
    } catch (err) {
        return 500
    }
}

let subject = {}
const getSubject = async (type) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-subject/',
        }).then(async (results) => {
            // console.log(results.data)
            subject = results.data
        })
        return subject
    } catch (err) {
        return 500
    }
}

let studentmaster = {}
const getStudentMaster = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-student-master/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            studentmaster = results.data
        })
        return studentmaster
    } catch (err) {
        return err
    }
}

let currsubj = {}
const getCurriculumSubject = async (curr, sem, gradelvl) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-curriculum-subject/' + curr + '/' + sem + '/' + gradelvl,
        }).then(async (results) => {
            // console.log(results.data)
            currsubj = results.data
        })
        return currsubj
    } catch (err) {
        return 500
    }
}

let milestone = {}
const getMilestone = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-milestone/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            milestone = results.data
        })
        return milestone
    } catch (err) {
        return 500
    }
}

let addmilestone = {}
const addMilestone = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-milestone/',
            data: data

        }).then(async (results) => {
            addmilestone = results.data
        })
        return addmilestone
    } catch (err) {
        return 500
    }
}

let updateenrollment = {}
const updateEnrollment = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/update-enrollment/',
            data: data

        }).then(async (results) => {
            updateenrollment = results.data
        })
        return updateenrollment
    } catch (err) {
        return 500
    }
}

let updatemilestone = {}
const updateMilestone = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/update-milestone/',
            data: data

        }).then(async (results) => {
            updatemilestone = results.data
        })
        return updatemilestone
    } catch (err) {
        return 500
    }
}

// launch
let buildings = {}
const getBuilding = async (type) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-building/',
        }).then(async (results) => {
            // console.log(results.data)
            buildings = results.data
        })
        return buildings
    } catch (err) {
        return 500
    }
}

let classrooms = {}
const getClassroom = async (type) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-classroom/',
        }).then(async (results) => {
            // console.log(results.data)
            classrooms = results.data
        })
        return classrooms
    } catch (err) {
        return 500
    }
}

let launch = {}
const getLaunch = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-launch/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            launch = results.data
        })
        return launch
    } catch (err) {
        return err
    }
}

let launchchecker = {}
const getLaunchChecker = async (ln_dtype, ln_quarter, ln_course, ln_gradelvl, ln_curriculum, ln_section, ln_year) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-launch-checker/',
            params: {
                ln_dtype: ln_dtype,
                ln_quarter: ln_quarter,
                ln_course: ln_course,
                ln_gradelvl: ln_gradelvl,
                ln_curriculum: ln_curriculum,
                ln_section: ln_section,
                ln_year: ln_year ? ln_year : 0
            },
        }).then(async (results) => {
            // console.log(results.data)
            launchchecker = results.data
        })
        return launchchecker
    } catch (err) {
        return err
    }
}

let launchresponse = {}
const addLaunch = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-launch/',
            data: data

        }).then(async (results) => {
            launchresponse = results.data
        })
        return launchresponse
    } catch (err) {
        return 500
    }
}

let scheduleresponse = {}
const addSchedule = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-schedule/',
            data: data

        }).then(async (results) => {
            scheduleresponse = results.data
        })
        return scheduleresponse
    } catch (err) {
        return 500
    }
}

let schedule = {}
const getSchedule = async (launchid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-schedule/' + launchid,
        }).then(async (results) => {
            // console.log(results.data)
            schedule = results.data
        })
        return schedule
    } catch (err) {
        return err
    }
}

let occupancy = {}
const getOccupancy = async (bid, classrid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-occupancy/' + bid + '/' + classrid,
        }).then(async (results) => {
            // console.log(results.data)
            occupancy = results.data
        })
        return occupancy
    } catch (err) {
        return err
    }
}

let occupancyothers = {}
const getOccupancyOthers = async (othersid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-occupancy-others/' + othersid,
        }).then(async (results) => {
            // console.log(results.data)
            occupancyothers = results.data
        })
        return occupancyothers
    } catch (err) {
        return err
    }
}

let employee = {}
const getEmployee = async (limit, offset, fname, mname, lname) => {
    let firstname = !fname ? '404' : fname
    let middlename = !mname ? '404' : mname
    let lastname = !lname ? '404' : lname
    try {
        await axios({
            method: "GET",
            url: 'api/get-employee/' + limit + '/' + offset + '/' + firstname + '/' + middlename + '/' + lastname,
        }).then(async (results) => {
            // console.log(results.data)
            employee = results.data
        })
        return employee
    } catch (err) {
        return err
    }
}

let scheduledsubjects = {}
const getScheduledSubjects = async (schedid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-scheduled-subjects/' + schedid,
        }).then(async (results) => {
            // console.log(results.data)
            scheduledsubjects = results.data
        })
        return scheduledsubjects
    } catch (err) {
        return err
    }
}


let addemployeeresponse = {}
const addEmployee = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-employee/',
            data: data

        }).then(async (results) => {
            addemployeeresponse = results.data
        })
        return addemployeeresponse
    } catch (err) {
        return 500
    }
}

let updateemployeeresponse = {}
const updateEmployee = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/update-employee/',
            data: data

        }).then(async (results) => {
            updateemployeeresponse = results.data
        })
        return updateemployeeresponse
    } catch (err) {
        return 500
    }
}

let addemployeeloadresponse = {}
const addEmployeeLoad = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-employee-load/',
            data: data

        }).then(async (results) => {
            addemployeeloadresponse = results.data
        })
        return addemployeeloadresponse
    } catch (err) {
        return 500
    }
}

let employeeload = {}
const getEmployeeLoad = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-employee-load/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            employeeload = results.data
        })
        return employeeload
    } catch (err) {
        return 500
    }
}

let deleteemployeeload = {}
const deleteEmployeeLoad = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/delete-employee-load/',
            data: data

        }).then(async (results) => {
            deleteemployeeload = results.data
        })
        return deleteemployeeload
    } catch (err) {
        return 500
    }

}

let deleteemployee = {}
const deleteEmployee = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/delete-employee/',
            data: data

        }).then(async (results) => {
            deleteemployee = results.data
        })
        return deleteemployee
    } catch (err) {
        return 500
    }

}

let deleteenrollment = {}
const deleteEnrollment = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/delete-enrollment/',
            data: data

        }).then(async (results) => {
            deleteenrollment = results.data
        })
        return deleteenrollment
    } catch (err) {
        return 500
    }

}

let addprogram = {}
const addProgram = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-program',
            data: data

        }).then(async (results) => {
            addprogram = results.data
        })
        return addprogram
    } catch (err) {
        return 500
    }
}

let addsection = {}
const addSection = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-section',
            data: data

        }).then(async (results) => {
            addsection = results.data
        })
        return addsection
    } catch (err) {
        return 500
    }
}

let addsubject = {}
const addSubject = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-subject',
            data: data

        }).then(async (results) => {
            addsubject = results.data
        })
        return addsubject
    } catch (err) {
        return 500
    }
}

let addcurriculum = {}
const addCurriculum = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-curriculum',
            data: data

        }).then(async (results) => {
            addcurriculum = results.data
        })
        return addcurriculum
    } catch (err) {
        return 500
    }
}

let addcurriculumtagging = {}
const addCurriculumTagging = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-curriculum-tagging',
            data: data

        }).then(async (results) => {
            addcurriculumtagging = results.data
        })
        return addcurriculumtagging
    } catch (err) {
        return 500
    }
}

let scheduledfaculty = {}
const getScheduledFaculty = async () => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-scheduled-faculty/',
        }).then(async (results) => {
            // console.log(results.data)
            scheduledfaculty = results.data
        })
        return scheduledfaculty
    } catch (err) {
        return 500
    }
}

let addscheduledfaculty = {}
const addScheduledFaculty = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-scheduled-faculty',
            data: data

        }).then(async (results) => {
            addscheduledfaculty = results.data
        })
        return addscheduledfaculty
    } catch (err) {
        return 500
    }
}

let deletescheduledfaculty = {}
const deleteScheduledFaculty = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/delete-scheduled-faculty/',
            data: data

        }).then(async (results) => {
            deletescheduledfaculty = results.data
        })
        return deletescheduledfaculty
    } catch (err) {
        return 500
    }

}

let facultyavilability = {}
const getFacultyAvailability = async () => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-faculty-availability/',
        }).then(async (results) => {
            // console.log(results.data)
            facultyavilability = results.data
        })
        return facultyavilability
    } catch (err) {
        return 500
    }
}


// accounting billing and cashier
let accountsdetails = {}
const getAccountsDetails = async () => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-details/',
        }).then(async (results) => {
            // console.log(results.data)
            accountsdetails = results.data
        })
        return accountsdetails
    } catch (err) {
        return 500
    }
}

let pricedetails = {}
const getPriceDetails = async () => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-price/',
        }).then(async (results) => {
            // console.log(results.data)
            pricedetails = results.data
        })
        return pricedetails
    } catch (err) {
        return 500
    }
}

let feedetails = {}
const getFeeDetails = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-fee/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            feedetails = results.data
        })
        return feedetails
    } catch (err) {
        return err
    }
}

let requestdetails = {}
const getTransactionDetails = async (limit, offset, fname, mname, lname, mode, id, type) => {
    let firstname = !fname ? '404' : fname
    let middlename = !mname ? '404' : mname
    let lastname = !lname ? '404' : lname

    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-request/' + limit + '/' + offset + '/' + firstname + '/' + middlename + '/' + lastname + '/' + mode + '/' + id + '/' + type,
        }).then(async (results) => {
            // console.log(results.data)
            requestdetails = results.data
        })
        return requestdetails
    } catch (err) {
        return err
    }
}

let addrequesteditem = {}
const addItemRequest = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-requested-item',
            data: data

        }).then(async (results) => {
            addrequesteditem = results.data
        })
        return addrequesteditem
    } catch (err) {
        return 500
    }
}

let deleteitemrequest = {}
const deleteItemRequest = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/delete-requested-item/',
            data: data

        }).then(async (results) => {
            deleteitemrequest = results.data
        })
        return deleteitemrequest
    } catch (err) {
        return 500
    }

}

let addpayment = {}
const addPayment = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-payment/',
            data: data

        }).then(async (results) => {
            addpayment = results.data
        })
        return addpayment
    } catch (err) {
        return 500
    }
}

let paymentdetails = {}
const getPaymentDetails = async (id, billtype) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-payment/' + id + '/' + billtype,
        }).then(async (results) => {
            // console.log(results.data)
            paymentdetails = results.data
        })
        return paymentdetails
    } catch (err) {
        return err
    }
}

let allpaymentdetails = {}
const getAllPayments = async (billtype, datefrom, dateto, cashier, access) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-payment-all/' + billtype + '/' + datefrom + '/' + dateto + '/' + cashier  + '/' + access,
        }).then(async (results) => {
            // console.log(results.data)
            allpaymentdetails = results.data
        })
        return allpaymentdetails
    } catch (err) {
        return err
    }
}

let addaccountingitem = {}
const addAccountingItem = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-accounting-item',
            data: data

        }).then(async (results) => {
            addaccountingitem = results.data
        })
        return addaccountingitem
    } catch (err) {
        return 500
    }
}

let clinicalStudents = {}
const addStudentClinicalRecord = async (data, type) => {
    if (type == 1) {
        try {
            await axios({
                method: "POST",
                url: 'api/add-clinical-students/',
                data: data

            }).then(async (results) => {
                clinicalStudents = results.data
            })
            return clinicalStudents
        } catch (err) {
            return 500
        }
    } else {

    }
}

let clinicalEmployee = {}
const addEmployeeClinicalRecord = async (data, type) => {
    if (type == 1) {
        try {
            await axios({
                method: "POST",
                url: 'api/add-clinical-employee/',
                data: data

            }).then(async (results) => {
                clinicalEmployee = results.data
            })
            return clinicalEmployee
        } catch (err) {
            return 500
        }
    } else {

    }
}


let studentclinicalrecords = {}
const getStudentClinicalRecords = async (mode, id) => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-students/' + mode + '/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            studentclinicalrecords = results.data
        })
        return studentclinicalrecords
    } catch (err) {
        return err
    }
}

let employeeclinicalrecords = {}
const getEmployeeClinicalRecords = async (mode, id) => {

    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-employees/' + mode + '/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            employeeclinicalrecords = results.data
        })
        return employeeclinicalrecords
    } catch (err) {
        return err
    }
}



let medicalsupplies = {}
const getMedicalSupplies = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-medical-supplies/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            medicalsupplies = results.data
        })
        return medicalsupplies
    } catch (err) {
        return err
    }
}

let medicalsupply = {}
const updateMedicalSupply = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-clinical-medical-supply/',
            data: data

        }).then(async (results) => {
            medicalsupply = results.data
        })
        return medicalsupply
    } catch (err) {
        return 500
    }
}



let dispensesupplystudent = {}
const dispenseMedicalSupplyStudent = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-clinical-medical-supply-dispense-student/',
            data: data

        }).then(async (results) => {
            dispensesupplystudent = results.data
        })
        return dispensesupplystudent
    } catch (err) {
        return 500
    }
}

let dispensedsuppliesstudent = {}
const getDispensedMedicalSupplyStudent = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-dispensed-supplies-student/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            dispensedsuppliesstudent = results.data
        })
        return dispensedsuppliesstudent
    } catch (err) {
        return err
    }
}

let dispensesupplyemployee = {}
const dispenseMedicalSupplyEmployee = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-clinical-medical-supply-dispense-employee/',
            data: data

        }).then(async (results) => {
            dispensesupplyemployee = results.data
        })
        return dispensesupplyemployee
    } catch (err) {
        return 500
    }
}

let dispensedsuppliesemployee = {}
const getDispensedMedicalSupplyEmployee = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-dispensed-supplies-employee/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            dispensedsuppliesemployee = results.data
        })
        return dispensedsuppliesemployee
    } catch (err) {
        return err
    }
}


let examinationishihara = {}
const addClinicIshihara = async (data, type, id, personid) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-clinical-examination-ishihara/' + type + '/' + id + '/' + personid,
            data: data

        }).then(async (results) => {
            examinationishihara = results.data
        })
        return examinationishihara
    } catch (err) {
        return 500
    }
}

let examinationhearing = {}
const addClinicHearing = async (data, type, id, personid) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-clinical-examination-hearing/' + type + '/' + id + '/' + personid,
            data: data

        }).then(async (results) => {
            examinationhearing = results.data
        })
        return examinationhearing
    } catch (err) {
        return 500
    }
}


let medicalheaders = {}
const getMedicalHeader = async (id, type) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-examination-header/' + id + '/' + type,
        }).then(async (results) => {
            // console.log(results.data)
            medicalheaders = results.data
        })
        return medicalheaders
    } catch (err) {
        return err
    }
}

let medicalishihara = {}
const getMedicalIshihara = async (id, headerid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-examination-ishihara/' + id + '/' + headerid,
        }).then(async (results) => {
            // console.log(results.data)
            medicalishihara = results.data
        })
        return medicalishihara
    } catch (err) {
        return err
    }
}

let medicalhearing = {}
const getMedicalHearing = async (id, headerid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-examination-hearing/' + id + '/' + headerid,
        }).then(async (results) => {
            // console.log(results.data)
            medicalhearing = results.data
        })
        return medicalhearing
    } catch (err) {
        return err
    }
}

let medicalfiles = {}
const getMedicalFiles = async (id, folder) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-medical-files/' + id + '/' + folder,
        }).then(async (results) => {
            // console.log(results.data)
            medicalfiles = results.data
        })
        return medicalfiles
    } catch (err) {
        return err
    }
}

let medicalfilheaders = {}
const getMedicalFileHeaders = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-clinical-medical-files-headers/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            medicalfilheaders = results.data
        })
        return medicalfilheaders
    } catch (err) {
        return err
    }
}

let medicalfilesheader = {}
const addMedicalFileHeader = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-clinical-medical-files-headers',
            data: data

        }).then(async (results) => {
            medicalfilesheader = results.data
        })
        return medicalfilesheader
    } catch (err) {
        return 500
    }
}

let medicalfileimage = {}
const uploadMedicalFileImage = async (data, location) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-clinical-medical-files-image/' + location,
            data: data

        }).then(async (results) => {
            medicalfileimage = results.data
        })
        return medicalfileimage
    } catch (err) {
        return 500
    }
}

let medicalfileimagelink = {}
const uploadMedicalFileLink = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-clinical-medical-files-image-upload',
            data: data

        }).then(async (results) => {
            medicalfileimagelink = results.data
        })
        return medicalfileimagelink
    } catch (err) {
        return 500
    }
}

let booksaccession = {}
const getBooksAccession = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-library-books-accession/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            booksaccession = results.data
        })
        return booksaccession
    } catch (err) {
        return err
    }
}

let booksddc = {}
const getBooksDdc = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-library-books-ddc/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            booksddc = results.data
        })
        return booksddc
    } catch (err) {
        return err
    }
}

let addbooksddc = {}
const addBooksDdc = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-library-books-ddc',
            data: data

        }).then(async (results) => {
            addbooksddc = results.data
        })
        return addbooksddc
    } catch (err) {
        return 500
    }
}

let addbookinformation = {}
const addBookInformation = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-library-books-information',
            data: data

        }).then(async (results) => {
            addbookinformation = results.data
        })
        return addbookinformation
    } catch (err) {
        return 500
    }
}

let enlistment = {}
const getEnlistment = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-enlistment/',
        }).then(async (results) => {
            // console.log(results.data)
            enlistment = results.data
        })
        return enlistment
    } catch (err) {
        return err
    }
}

let addborrowedbooks = {}
const addBorrowedBooks = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-library-books-borrowed',
            data: data

        }).then(async (results) => {
            addborrowedbooks = results.data
        })
        return addborrowedbooks
    } catch (err) {
        return 500
    }
}

let getborrowedbooks = {}
const getBorrowedBooks = async (limit, offset, id) => {
    let search = id
    if (!id) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-library-books-borrowed/' + limit + '/' + offset + '/' + search,
        }).then(async (results) => {
            // console.log(results.data)
            getborrowedbooks = results.data
        })
        return getborrowedbooks
    } catch (err) {
        return err
    }
}

let updateborrowedbooks = {}
const updateBorrowedBooks = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/update-library-books-borrowed',
            data: data

        }).then(async (results) => {
            updateborrowedbooks = results.data
        })
        return updateborrowedbooks
    } catch (err) {
        return 500
    }
}

let getlibrarycardissue = {}
const getLibraryCardIssue = async (personid, enrid, active) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-library-card-issue/' + personid + '/' + enrid + '/' + active,
        }).then(async (results) => {
            // console.log(results.data)
            getlibrarycardissue = results.data
        })
        return getlibrarycardissue
    } catch (err) {
        return err
    }
}

let borrowedby = {}
const getBorrowedBooksBy = async (cardid, personid, enrid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-library-books-borrowed-by/' + cardid + '/' + personid + '/' + enrid,
        }).then(async (results) => {
            // console.log(results.data)
            borrowedby = results.data
        })
        return borrowedby
    } catch (err) {
        return err
    }
}

let deactivatelibrarycard = {}
const deactivateLibraryCard = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/deactivate-library-card',
            data: data

        }).then(async (results) => {
            deactivatelibrarycard = results.data
        })
        return deactivatelibrarycard
    } catch (err) {
        return 500
    }
}

let addlibrarycard = {}
const addLibraryCard = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-library-card',
            data: data

        }).then(async (results) => {
            addlibrarycard = results.data
        })
        return addlibrarycard
    } catch (err) {
        return 500
    }
}

let facultyclass = {}
const getFacultyClass = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-faculty-class/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            facultyclass = results.data
        })
        return facultyclass
    } catch (err) {
        return 500
    }
}

let facultyassignment = {}
const getFacultyAssignment = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-faculty-assignment/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            facultyassignment = results.data
        })
        return facultyassignment
    } catch (err) {
        return 500
    }
}

let facultystudent = {}
const getFacultyStudent = async (section, gradelvl, course) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-faculty-student/' + section + '/' + gradelvl + '/' + course,
        }).then(async (results) => {
            // console.log(results.data)
            facultystudent = results.data
        })
        return facultystudent
    } catch (err) {
        return 500
    }
}

let facultystudentmilestone = {}
const getFacultyStudentMilestone = async (section, gradelvl, course) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-faculty-student-milestone/' + section + '/' + gradelvl + '/' + course,
        }).then(async (results) => {
            // console.log(results.data)
            facultystudentmilestone = results.data
        })
        return facultystudentmilestone
    } catch (err) {
        return 500
    }
}

let gradingsheetheader = {}
const getGradingSheetHeader = async (lnid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-faculty-grading-sheet-header/' + lnid,
        }).then(async (results) => {
            // console.log(results.data)
            gradingsheetheader = results.data
        })
        return gradingsheetheader
    } catch (err) {
        return 500
    }
}

let gradingsheetgrade = {}
const getGradingSheetGrade = async (hid, subjid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-faculty-grading-sheet-grade/' + hid + '/' + subjid,
        }).then(async (results) => {
            // console.log(results.data)
            gradingsheetgrade = results.data
        })
        return gradingsheetgrade
    } catch (err) {
        return 500
    }
}

let addgradingheader = {}
const addGradingSheet = async (data, type) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-faculty-grading-header/' + type,
            data: data

        }).then(async (results) => {
            addgradingheader = results.data
        })
        return addgradingheader
    } catch (err) {
        return 500
    }
}

let addgradinggrade = {}
const addGradingGrade = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-faculty-grading-grade',
            data: data

        }).then(async (results) => {
            addgradinggrade = results.data
        })
        return addgradinggrade
    } catch (err) {
        return 500
    }
}

let commanddropapplicant = {}
const commandDropApplicant = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/command-drop-applicant',
            data: data

        }).then(async (results) => {
            commanddropapplicant = results.data
        })
        return commanddropapplicant
    } catch (err) {
        return 500
    }
}

let studentidentification = {}
const getStudentIdentification = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-student-identification/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            studentidentification = results.data
        })
        return studentidentification
    } catch (err) {
        return 500
    }
}

let addstudentidentity = {}
const addStudentIdentification = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-student-identification',
            data: data

        }).then(async (results) => {
            addstudentidentity = results.data
        })
        return addstudentidentity
    } catch (err) {
        return 500
    }
}

let commandsettings = {}
const setCommandUpdate = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-command-update',
            data: data

        }).then(async (results) => {
            commandsettings = results.data
        })
        return commandsettings
    } catch (err) {
        return 500
    }
}

let commandsettingsdata = {}
const getCommandUpdate = async () => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-command-update/',
        }).then(async (results) => {
            // console.log(results.data)
            commandsettingsdata = results.data
        })
        return commandsettingsdata
    } catch (err) {
        return 500
    }
}

let commandsettingsdatacurr = {}
const getCommandUpdateCurriculum = async (prog, grad, course) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-command-update-curriculum/' + prog + '/' + grad + '/' + course,
        }).then(async (results) => {
            // console.log(results.data)
            commandsettingsdatacurr = results.data
        })
        return commandsettingsdatacurr
    } catch (err) {
        return 500
    }
}

let commandsettingusers = {}
const getCommandUsers = async (userid) => {
    let search = userid
    if (!userid) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-command-users/' + search,
        }).then(async (results) => {
            commandsettingusers = results.data
        })
        return commandsettingusers
    } catch (err) {
        return err
    }
}

let commandsettingsuserupdate = {}
const updateCommandUsers = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/update-command-user',
            data: data

        }).then(async (results) => {
            commandsettingsuserupdate = results.data
        })
        return commandsettingsuserupdate
    } catch (err) {
        return 500
    }
}

let commandsettingregister = {}
const addCommandUsers = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-command-user',
            data: data

        }).then(async (results) => {
            commandsettingregister = results.data
        })
        return commandsettingregister
    } catch (err) {
        return 500
    }
}

let commandsettingaccess = {}
const saveCommandAccess = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-command-access',
            data: data

        }).then(async (results) => {
            commandsettingaccess = results.data
        })
        return commandsettingaccess
    } catch (err) {
        return 500
    }
}

let commandaccess = {}
const getCommandAccess = async (userid) => {
    let search = userid
    if (!userid) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-command-access/' + search,
        }).then(async (results) => {
            commandaccess = results.data
        })
        return commandaccess
    } catch (err) {
        return err
    }
}

let tagempacc = {}
const employeeAccountTag = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/tag-employee-account',
            data: data

        }).then(async (results) => {
            tagempacc = results.data
        })
        return tagempacc
    } catch (err) {
        return 500
    }
}

let employeeaccount = {}
const getEmployeeAccount = async (userid) => {
    let search = userid
    if (!userid) {
        search = 204
    }
    try {
        await axios({
            method: "GET",
            url: 'api/get-employee-account/' + search,
        }).then(async (results) => {
            employeeaccount = results.data
        })
        return employeeaccount
    } catch (err) {
        return err
    }
}

let qrdata = {}
const getQRData = async (userid, mode) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-qr-data/' + userid + '/' + mode,
        }).then(async (results) => {
            qrdata = results.data.data
        })
        return qrdata
    } catch (err) {
        return err
    }
}

let setacademicstatus = {}
const setAcademicStatus = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/set-academic-status',
            data: data

        }).then(async (results) => {
            setacademicstatus = results.data
        })
        return setacademicstatus
    } catch (err) {
        return 500
    }
}

let getacademic = {}
const getAcademicStatus = async (mode, code) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-academic-status/' + mode + '/' + code,

        }).then(async (results) => {
            getacademic = results.data
        })
        return getacademic
    } catch (err) {
        return 500
    }
}

let getarchivemerge = {}
const getArchiveMerge = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-archive-merge/' + id,

        }).then(async (results) => {
            getarchivemerge = results.data
        })
        return getarchivemerge
    } catch (err) {
        return 500
    }
}

let alumni = {}
const getAlumniStudents = async (limit, offset, fname, mname, lname, mode) => {
    let firstname = !fname ? '404' : fname
    let middlename = !mname ? '404' : mname
    let lastname = !lname ? '404' : lname
    try {
        await axios({
            method: "GET",
            url: 'api/get-alumni-students/' + limit + '/' + offset + '/' + firstname + '/' + middlename + '/' + lastname + '/' + mode,
        }).then(async (results) => {
            // console.log(results.data)
            alumni = results.data
        })
        return alumni
    } catch (err) {
        return err
    }
}

let updatearchivedetails = {}
const updateArchiveDetails = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/update-archive-details',
            data: data

        }).then(async (results) => {
            updatearchivedetails = results.data
        })
        return updatearchivedetails
    } catch (err) {
        return 500
    }
}

let getenrollmentschedule = {}
const getEnrollmentSchedule = async (curr, prog, grad, cour, sec, lnid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-enrollment-schedule/' + curr + '/' + prog + '/' + grad + '/' + cour + '/' + sec + '/' + lnid,
        }).then(async (results) => {
            // console.log(results.data)
            getenrollmentschedule = results.data
        })
        return getenrollmentschedule
    } catch (err) {
        return err
    }
}

let getmergedclass = {}
const getMergedClass = async (schedid, day) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-merged-class/' + schedid + '/' + day,
        }).then(async (results) => {
            // console.log(results.data)
            getmergedclass = results.data
        })
        return getmergedclass
    } catch (err) {
        return err
    }
}

const getCurrentWeekDailyCollection = async () => {
    // 🗓 Get start (Monday) and end (Sunday) of the current week
    const getCurrentWeekRange = () => {
        const now = new Date();
        const day = now.getDay(); // Sunday = 0, Monday = 1, ...
        const diffToMonday = (day === 0 ? -6 : 1) - day; // adjust to Monday start

        const monday = new Date(now);
        monday.setDate(now.getDate() + diffToMonday);
        monday.setHours(0, 0, 0, 0);

        const sunday = new Date(monday);
        sunday.setDate(monday.getDate() + 6);
        sunday.setHours(23, 59, 59, 999);

        return { monday, sunday };
    };

    // 🧮 Format date as YYYY-MM-DD HH:mm:ss
    const formatDate = (d) => {
        const pad = (num) => String(num).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    };

    // 📅 Day names
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    // 🕒 Get week range and formatted values
    const weekRange = getCurrentWeekRange();
    const formattedWeekStart = formatDate(weekRange.monday);
    const formattedWeekEnd = formatDate(weekRange.sunday);
    const currentDay = days[new Date().getDay()];

   
    // Return everything in one object
    return {
        weekRange,
        formattedWeekStart,
        formattedWeekEnd,
        currentDay,
        days,
    };
};

const getBarHeights = async (collections) => {
    // 💰 Example: daily total collections
    // const dailyCollections = [120, 300, 250, 400, 180, 220, 100]; // Mon → Sun
    // 📊 Normalize heights (so the tallest bar = 100%)
    const maxCollection = Math.max(...collections);
    const barHeights = collections.map((val) =>
        Math.round((val / maxCollection) * 100)
    );

    return{
        barHeights,
        collections
    };

}

const dateFormatterWord = async (toBeFormatted) => {
    // Convert to a Date object
    let date = new Date(toBeFormatted);
    // Format it
    let options = { year: 'numeric', month: 'long', day: 'numeric' };
    let formattedDate = new Intl.DateTimeFormat('en-US', options).format(date);

    return{
        formattedDate
    }
}

let setseries = {}
const getSetSeries = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-set-series/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            setseries = results.data
        })
        return setseries
    } catch (err) {
        return err
    }
}

let savesetseries = {}
const saveSetSeries = async (data, mode) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-set-series/' + mode,
            data:data
        }).then(async (results) => {
            // console.log(results.data)
            savesetseries = results.data
        })
        return savesetseries
    } catch (err) {
        return err
    }
}

let usedseries = {}
const getUsedSeries = async (seriesstart, serieslimit, year, prefix) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-used-series/' + seriesstart + '/'+ serieslimit + '/'+ year + '/'+ prefix,
        }).then(async (results) => {
            // console.log(results.data)
            usedseries = results.data
        })
        return usedseries
    } catch (err) {
        return err
    }
}

let cashiersdata = {}
const getCashiersDetails = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-cashier-details/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            cashiersdata = results.data
        })
        return cashiersdata
    } catch (err) {
        return err
    }
}

let collectionstatus = {}
const turnOffCollection = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/set-collection-status/',
            data:data
        }).then(async (results) => {
            // console.log(results.data)
            collectionstatus = results.data
        })
        return collectionstatus
    } catch (err) {
        return err
    }
}

const getCollectionStatus = async () => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-collection-status/',
        }).then(async (results) => {
            // console.log(results.data)
            collectionstatus = results.data
        })
        return collectionstatus
    } catch (err) {
        return err
    }
}

let accountitems = {}
const getAccountsFee = async (id, item) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-fee/' + id + '/' + item,
        }).then(async (results) => {
            // console.log(results.data)
            accountitems = results.data
        })
        return accountitems
    } catch (err) {
        return err
    }
}

let tuitioninfo = {}
const getTuitionInformation = async (limit, offset, mode, item) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-tuition/' + limit + '/' + offset + '/' + mode + '/' + item,
        }).then(async (results) => {
            // console.log(results.data)
            tuitioninfo = results.data
        })
        return tuitioninfo
    } catch (err) {
        return err
    }
}

let edittuitionresponse = {}
const editAccountingTuition = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/set-accounts-tuition/',
            data:data
        }).then(async (results) => {
            // console.log(results.data)
            edittuitionresponse = results.data
        })
        return edittuitionresponse
    } catch (err) {
        return err
    }
}

let tuitiontemplate = {}
const saveTuitionTemplate = async (data, mode) => {
    try {
        await axios({
            method: "POST",
            url: 'api/set-account-tuition-template/'+ mode,
            data:data
        }).then(async (results) => {
            // console.log(results.data)
            tuitiontemplate = results.data
        })
        return tuitiontemplate
    } catch (err) {
        return err
    }
}

let gettuitiontemplate = {}
const getTuitionTemplate = async (id) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-tuition-template/' + id,
        }).then(async (results) => {
            // console.log(results.data)
            gettuitiontemplate = results.data
        })
        return gettuitiontemplate
    } catch (err) {
        return err
    }
}

let getaccountsubjects = {}
const getSubjectsFromRegistrar = async (course, gradelvl, sem, curr) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-accounts-subjects/' + course + '/' + gradelvl+ '/' + sem + '/' + curr,
        }).then(async (results) => {
            // console.log(results.data)
            getaccountsubjects = results.data
        })
        return getaccountsubjects
    } catch (err) {
        return err
    }
}

let getchargesheader = {}
const getChargesTemplateHeader = async (curriculum, sem, program, course, gradelvl, section) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-charge-header/' + curriculum + '/' + sem+ '/' + program + '/' + course+ '/' + gradelvl+ '/' + section,
        }).then(async (results) => {
            // console.log(results.data)
            getchargesheader = results.data
        })
        return getchargesheader
    } catch (err) {
        return err
    }
}

let gettotalcharges = {}
const getTotalCharges = async (curriculum, sem, program, course, gradelvl, section, enrid, personid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-charge-total/' + curriculum + '/' + sem+ '/' + program + '/' + course+ '/' + gradelvl+ '/' + section + '/' + enrid + '/' + personid,
        }).then(async (results) => {
            // console.log(results.data)
            gettotalcharges = results.data
        })
        return gettotalcharges
    } catch (err) {
        return err
    }
}

let getstudentaccount = {}
const getStudentAccount = async (perid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-student-account/' + perid,
        }).then(async (results) => {
            // console.log(results.data)
            getstudentaccount = results.data
        })
        return getstudentaccount
    } catch (err) {
        return err
    }
}

let getsettlementdetails = {}
const getSettlementDetails = async (perid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-settlement-details/' + perid,
        }).then(async (results) => {
            // console.log(results.data)
            getsettlementdetails = results.data
        })
        return getsettlementdetails
    } catch (err) {
        return err
    }
}

let getscholarshipdetails = {}
const getScholarshipDetails = async (perid) => {
    try {
        await axios({
            method: "GET",
            url: 'api/get-scholarship-details/' + perid,
        }).then(async (results) => {
            // console.log(results.data)
            getscholarshipdetails = results.data
        })
        return getscholarshipdetails
    } catch (err) {
        return err
    }
}

let addscholarshipdetails = {}
const addScholarshipDetails = async (data) => {
    try {
        await axios({
            method: "POST",
            url: 'api/add-scholarship-details/',
            data:data
        }).then(async (results) => {
            // console.log(results.data)s
            addscholarshipdetails = results.data
        })
        return addscholarshipdetails
    } catch (err) {
        return err
    }
}


export {

    getApplicant,
    getAlumniStudents,
    getGender,
    getNationality,
    getCivilStatus,
    getGradelvl,
    getProgram,
    getQuarter,
    getDegree,
    getProgramList,
    getSemester,
    getSection,
    getDemograph,
    getAcademicDefaults,
    getCountry,
    getRegion,
    getProvince,
    getCity,
    getBarangay,

    addApplicant,
    getPerson,
    getFamily,
    getAward,
    getAttainment,
    updateApplicant,
    updatePersonDetails,
    deleteFamAwrAtt,
    enrollApplicant,
    getEnrollment,
    getStudent,
    getStudentFiltering,
    getStudentIdDetails,
    getStudentByCourse,
    uploadProfile,
    uploadLinkProfile,
    uploadSignature,
    uploadLinkSignature,
    getCurriculum,
    getCurriculumStudent,
    getTaggedSubject,
    getSubject,
    getCurriculumSubject,
    getMilestone,
    addMilestone,
    updateEnrollment,
    updateMilestone,

    getBuilding,
    getClassroom,
    getLaunch,
    getLaunchChecker,
    addLaunch,
    addSchedule,
    getSchedule,
    getOccupancy,
    getOccupancyOthers,

    getEmployee,
    addEmployee,
    updateEmployee,
    addEmployeeLoad,
    getEmployeeLoad,
    deleteEmployeeLoad,
    deleteEmployee,
    deleteEnrollment,
    addProgram,
    addSection,
    addSubject,
    addCurriculum,
    getCurriculumSett,
    addCurriculumTagging,
    getSpecialization,
    getStudentMaster,
    getScheduledSubjects,
    getScheduledFaculty,
    addScheduledFaculty,
    deleteScheduledFaculty,
    getFacultyAvailability,

    getAccountsDetails,
    getPriceDetails,
    getFeeDetails,
    getTransactionDetails,
    addItemRequest,
    deleteItemRequest,
    addPayment,
    getPaymentDetails,
    getAllPayments,
    addAccountingItem,

    addStudentClinicalRecord,
    addEmployeeClinicalRecord,
    getStudentClinicalRecords,
    getEmployeeClinicalRecords,
    getMedicalSupplies,
    updateMedicalSupply,
    dispenseMedicalSupplyStudent,
    getDispensedMedicalSupplyStudent,
    dispenseMedicalSupplyEmployee,
    getDispensedMedicalSupplyEmployee,
    addClinicIshihara,
    addClinicHearing,
    getMedicalIshihara,
    getMedicalHearing,
    getMedicalHeader,
    getMedicalFiles,
    getMedicalFileHeaders,
    addMedicalFileHeader,
    uploadMedicalFileImage,
    uploadMedicalFileLink,

    getBooksAccession,
    getBooksDdc,
    addBooksDdc,
    addBookInformation,
    getEnlistment,
    addBorrowedBooks,
    getBorrowedBooks,
    updateBorrowedBooks,
    getLibraryCardIssue,
    getBorrowedBooksBy,
    deactivateLibraryCard,
    addLibraryCard,

    getFacultyClass,
    getFacultyAssignment,
    getFacultyStudent,
    getFacultyStudentMilestone,
    getGradingSheetHeader,
    getGradingSheetGrade,
    addGradingSheet,
    addGradingGrade,
    commandDropApplicant,
    getStudentIdentification,
    addStudentIdentification,
    setCommandUpdate,
    getCommandUpdate,
    getCommandUpdateCurriculum,
    getCommandUsers,
    updateCommandUsers,
    addCommandUsers,
    saveCommandAccess,
    getCommandAccess,
    employeeAccountTag,
    getEmployeeAccount,
    getQRData,
    setAcademicStatus,
    getAcademicStatus,
    getArchiveMerge,
    updateArchiveDetails,
    getEnrollmentSchedule,
    getMergedClass,
    getCurrentWeekDailyCollection,
    dateFormatterWord,
    getBarHeights,
    getSetSeries,
    saveSetSeries,
    getUsedSeries,
    getCashiersDetails,
    turnOffCollection,
    getCollectionStatus,
    getAccountsFee,
    getTuitionInformation,
    editAccountingTuition,
    saveTuitionTemplate,
    getTuitionTemplate,
    getSubjectsFromRegistrar,
    getChargesTemplateHeader,
    getTotalCharges,
    getStudentAccount,
    getSettlementDetails,
    getScholarshipDetails,
    addScholarshipDetails
}

