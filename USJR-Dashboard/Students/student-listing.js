let currentRow = null;
let editId = document.getElementById('editID');
let editLastName = document.getElementById('editLastName');
let editFirstName = document.getElementById('editFirstName');
let editMidName = document.getElementById('editMidName');
let editCollege = document.getElementById('editCollege');
let editProgram = document.getElementById('editProgram');
let editYear = document.getElementById('editYear');
let editPanel = document.getElementById('editPanel');

function closeme() {
    editPanel.style.display = "none";
}

axios.get('get-students.php')
.then(response => {
    console.log('Response data:', response.data);
    return response.data;
})
.then(data => {
    console.log('Processed data:', data);
    const tablebody = document.getElementById('table-body');
    data.forEach(row => {
        const tr = document.createElement('tr');
        const edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="fill: green" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>';
        const del = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="fill: red" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/></svg>';
        const midName = row.studmidname ? row.studmidname.charAt(0) : 'N/A'; 
        
        tr.innerHTML = `
            <td class="iamid">${row.studid}</td>
            <td>${row.studlastname}</td>
            <td>${row.studfirstname}</td>
            <td>${midName}</td>
            <td>${row.collfullname ? row.collfullname : 'N/A'}</td>
            <td>${row.progfullname ? row.progfullname : 'N/A'}</td>
            <td>${row.studyear}</td>
            <td><button class="btn btn-light edituser">${edit}</button><button class="btn btn-light deleteuser">${del}</button></td>
        `;
        tablebody.appendChild(tr);

        tr.querySelector('.edituser').addEventListener('click', () => {
            editCollege.innerHTML = '';
            editProgram.innerHTML = '';

            currentRow = tr;
            editId.value = row.studid;
            editLastName.value = row.studlastname;
            editFirstName.value = row.studfirstname;
            editMidName.value = row.studmidname;
            editYear.value = row.studyear;

            let collegeOption = document.createElement('option');
            collegeOption.value = row.studcollid;
            collegeOption.textContent = row.collfullname;
            collegeOption.selected = true;
            editCollege.appendChild(collegeOption);

            editPanel.style.display = "block";

            console.log("Program ID:", row.studprogid);
            console.log("Program Name:", row.progfullname);

            fetchCollegesAndPrograms(row.studcollid, row.studprogid, row.progfullname);
        });
        tr.querySelector('.deleteuser').addEventListener('click', () => {
            currentRow = tr;
            const id = currentRow.querySelector('.iamid').innerText;

            Swal.fire({
                title: "Are you sure?",
                text: "This action will permanently delete the student.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel",
                allowEscapeKey: false,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('editStudent.php', {
                        id: id,
                        action: "delete"
                    })
                    .then(response => {
                        console.log(response.data);
                        if (response.data.status === 'success') {
                            currentRow.remove();
                            currentRow = null;
                            Swal.fire({
                                icon: "success",
                                title: "Student deleted successfully",
                                timer: 3000,
                                timerProgressBar: true
                            });
                        } else {
                            alert('Failed to delete student' + response.data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error: ', error);
                    });
                }
            });
        });
    });
})
.catch(error => {
    console.error('Error:', error);
});

let edit = document.querySelector('.saveedit');
edit.addEventListener('click', () => {
    if (currentRow) {
        const id = editId.value;
        const lname = editLastName.value;
        const fname = editFirstName.value;
        const mname = editMidName.value;
        const college = editCollege.value;
        const program = editProgram.value;
        const year = editYear.value;

        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to save the changes?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, save it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('editStudent.php', {
                    id: id,
                    lname: lname,
                    fname: fname,
                    mname: mname,
                    college: college,
                    program: program,
                    year: year,
                    action: "edit"
                })
                .then(response => {
                    console.log('Server Response:', response.data);
                    if (response.data.status === 'success') {
                        console.log("Update successful: ", response.data.message);
                        currentRow.querySelector('td:nth-child(2)').innerText = lname;
                        currentRow.querySelector('td:nth-child(3)').innerText = fname;
                        currentRow.querySelector('td:nth-child(4)').innerText = mname;
                        currentRow.querySelector('td:nth-child(5)').innerText = editCollege.options[editCollege.selectedIndex].textContent;
                        currentRow.querySelector('td:nth-child(6)').innerText = editProgram.textContent;
                        currentRow.querySelector('td:nth-child(7)').innerText = year;
            
                        Swal.fire({
                            icon: "success",
                            title: "Student updated successfully",
                            timer: 3000,
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            timerProgressBar: true
                        }).then(() => {
                            editPanel.style.display = "none";
                            location.reload();
                        });
                    } else {
                        console.error('Error: ', response.data.message);
                        Swal.fire({
                            icon: "error",
                            title: "Update Failed",
                            text: response.data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An error occurred while updating the college"
                    });
                    console.error('Error: ', error);
                });
            }
        })
    }
    editPanel.style.display = "none";
});

function fetchCollegesAndPrograms(selectedCollegeId, selectedProgramId, selectedProgram) {
    axios.get('../get-colleges-programs.php')
        .then(response => {
            console.log(response.data); 
            const colleges = response.data.colleges;
            const programs = response.data.programs;

            colleges.forEach(college => {
                if (college.collid !== selectedCollegeId) {
                    const option = document.createElement('option');
                    option.value = college.collid;
                    option.textContent = college.collfullname;
                    editCollege.appendChild(option);
                }
                
            });

            editCollege.addEventListener('change', () => {
                editProgram.innerHTML = '';

                const defaultOption = document.createElement('option');
                defaultOption.value = selectedProgramId;
                defaultOption.textContent = selectedProgram;
                editProgram.appendChild(defaultOption);

                const selectedCollegeId = editCollege.value;
                programs.forEach(program => {
                    if (program.progcollid == selectedCollegeId) {
                        if (program.progid !== selectedProgramId) {
                            const option = document.createElement('option');
                            option.value = program.progid;
                            option.textContent = program.progfullname;
                            editProgram.appendChild(option);
                        }
                    }
                });
            });
            if (selectedCollegeId) {
                editCollege.value = selectedCollegeId;
                editCollege.dispatchEvent(new Event('change'));
            }
            if (selectedProgramId) {
                editProgram.value = selectedProgramId;
            }
        })
        .catch(error => {
            console.error('Error fetching colleges and programs:', error);
        });
}