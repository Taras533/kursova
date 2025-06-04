function openAddPlayerModal() {
  const modalContainer = document.getElementById("modal-container");
  modalContainer.innerHTML = `
  <div class="modal fade" id="addPlayerModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addPlayerModalLabel">Додати гравця</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addPlayerForm" method="post" action="add_player.php" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="playerFirstName" class="form-label">Ім’я</label>
              <input type="text" name="first_name" id="playerFirstName" class="form-control" placeholder="Введіть ім'я" required />
            </div>
            <div class="mb-3">
              <label for="playerLastName" class="form-label">Прізвище</label>
              <input type="text" name="last_name" id="playerLastName" class="form-control" placeholder="Введіть прізвище" required />
            </div>
            <div class="mb-3">
              <label for="playerBirthDate" class="form-label">Дата народження</label>
              <input type="date" name="birth_date" id="playerBirthDate" class="form-control" required />
            </div>
            <div class="mb-3">
              <label for="playerNationality" class="form-label">Національність</label>
              <input type="text" name="nationality" id="playerNationality" class="form-control" placeholder="Введіть національність" required />
            </div>
            <div class="mb-3">
              <label for="playerPosition" class="form-label">Позиція</label>
              <select name="position" id="playerPosition" class="form-select" required>
                <option value="Воротар">Воротар</option>
                <option value="Захисник">Захисник</option>
                <option value="Півзахисник">Півзахисник</option>
                <option value="Нападник">Нападник</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="playerJerseyNumber" class="form-label">Номер футболки</label>
              <input type="number" name="jersey_number" id="playerJerseyNumber" class="form-control" required min="1" max="99" />
            </div>
            <div class="mb-3">
              <label for="playerPhoto" class="form-label">Фото (JPEG/PNG/WebP, до 2MB)</label>
              <input type="file" name="photo" id="playerPhoto" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
          <button type="submit" form="addPlayerForm" class="btn btn-success">Додати</button>
        </div>
      </div>
    </div>
  </div>
  `;
  const addPlayerModal = new bootstrap.Modal(
    document.getElementById("addPlayerModal")
  );
  addPlayerModal.show();
}

function openAddCoachModal() {
  const modalContainer = document.getElementById("modal-container");
  modalContainer.innerHTML = `
  <div class="modal fade" id="addCoachModal" tabindex="-1" aria-labelledby="addCoachModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCoachModalLabel">Додати тренера</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addCoachForm" method="post" action="add_coach.php" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="coachFirstName" class="form-label">Ім’я</label>
              <input type="text" name="first_name" id="coachFirstName" class="form-control" placeholder="Введіть ім'я" required />
            </div>
            <div class="mb-3">
              <label for="coachLastName" class="form-label">Прізвище</label>
              <input type="text" name="last_name" id="coachLastName" class="form-control" placeholder="Введіть прізвище" required />
            </div>
            <div class="mb-3">
              <label for="coachPosition" class="form-label">Посада</label>
              <input type="text" name="position" id="coachPosition" class="form-control" placeholder="Введіть посаду" required />
            </div>
            <div class="mb-3">
              <label for="coachPhoto" class="form-label">Фото</label>
              <input type="file" name="photo" id="coachPhoto" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
          <button type="submit" form="addCoachForm" class="btn btn-success">Додати</button>
        </div>
      </div>
    </div>
  </div>
  `;
  const addCoachModal = new bootstrap.Modal(
    document.getElementById("addCoachModal")
  );
  addCoachModal.show();
}

// Функція для відкриття модального вікна редагування гравця
function openEditPlayerModal(playerData) {
  const modalContainer = document.getElementById("modal-container");
  modalContainer.innerHTML = `
  <div class="modal fade" id="editPlayerModal" tabindex="-1" aria-labelledby="editPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editPlayerModalLabel">Редагувати гравця: ${
            playerData.first_name
          } ${playerData.last_name}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editPlayerForm" method="post" action="edit_player.php" enctype="multipart/form-data">
            <input type="hidden" name="player_id" value="${
              playerData.player_id
            }">
            <div class="mb-3">
              <label for="editPlayerFirstName" class="form-label">Ім’я</label>
              <input type="text" name="first_name" id="editPlayerFirstName" class="form-control" value="${
                playerData.first_name
              }" required />
            </div>
            <div class="mb-3">
              <label for="editPlayerLastName" class="form-label">Прізвище</label>
              <input type="text" name="last_name" id="editPlayerLastName" class="form-control" value="${
                playerData.last_name
              }" required />
            </div>
            <div class="mb-3">
              <label for="editPlayerBirthDate" class="form-label">Дата народження</label>
              <input type="date" name="birth_date" id="editPlayerBirthDate" class="form-control" value="${
                playerData.birth_date
              }" required />
            </div>
            <div class="mb-3">
              <label for="editPlayerNationality" class="form-label">Національність</label>
              <input type="text" name="nationality" id="editPlayerNationality" class="form-control" value="${
                playerData.nationality
              }" required />
            </div>
            <div class="mb-3">
              <label for="editPlayerPosition" class="form-label">Позиція</label>
              <select name="position" id="editPlayerPosition" class="form-select" required>
                <option value="Воротар" ${
                  playerData.position === "Воротар" ? "selected" : ""
                }>Воротар</option>
                <option value="Захисник" ${
                  playerData.position === "Захисник" ? "selected" : ""
                }>Захисник</option>
                <option value="Півзахисник" ${
                  playerData.position === "Півзахисник" ? "selected" : ""
                }>Півзахисник</option>
                <option value="Нападник" ${
                  playerData.position === "Нападник" ? "selected" : ""
                }>Нападник</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="editPlayerJerseyNumber" class="form-label">Номер футболки</label>
              <input type="number" name="jersey_number" id="editPlayerJerseyNumber" class="form-control" value="${
                playerData.jersey_number
              }" required min="1" max="99" />
            </div>
            <div class="mb-3">
              <label for="editPlayerPhoto" class="form-label">Фото (залиште пустим, якщо не змінюєте)</label>
              <input type="file" name="photo" id="editPlayerPhoto" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
          <button type="submit" form="editPlayerForm" class="btn btn-primary">Зберегти зміни</button>
        </div>
      </div>
    </div>
  </div>
  `;
  const editPlayerModal = new bootstrap.Modal(
    document.getElementById("editPlayerModal")
  );
  editPlayerModal.show();
}

// Функція для відкриття модального вікна редагування тренера
function openEditCoachModal(coachData) {
  const modalContainer = document.getElementById("modal-container");
  modalContainer.innerHTML = `
  <div class="modal fade" id="editCoachModal" tabindex="-1" aria-labelledby="editCoachModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editCoachModalLabel">Редагувати тренера: ${coachData.first_name} ${coachData.last_name}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editCoachForm" method="post" action="edit_coach.php" enctype="multipart/form-data">
            <input type="hidden" name="coach_id" value="${coachData.coach_id}">
            <div class="mb-3">
              <label for="editCoachFirstName" class="form-label">Ім’я</label>
              <input type="text" name="first_name" id="editCoachFirstName" class="form-control" value="${coachData.first_name}" required />
            </div>
            <div class="mb-3">
              <label for="editCoachLastName" class="form-label">Прізвище</label>
              <input type="text" name="last_name" id="editCoachLastName" class="form-control" value="${coachData.last_name}" required />
            </div>
            <div class="mb-3">
              <label for="editCoachPosition" class="form-label">Посада</label>
              <input type="text" name="position" id="editCoachPosition" class="form-control" value="${coachData.position}" required />
            </div>
            <div class="mb-3">
              <label for="editCoachPhoto" class="form-label">Фото (залиште пустим, якщо не змінюєте)</label>
              <input type="file" name="photo" id="editCoachPhoto" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
          <button type="submit" form="editCoachForm" class="btn btn-primary">Зберегти зміни</button>
        </div>
      </div>
    </div>
  </div>
  `;
  const editCoachModal = new bootstrap.Modal(
    document.getElementById("editCoachModal")
  );
  editCoachModal.show();
}

// Загальна функція для закриття модальних вікон - більше не потрібна
// Bootstrap modals керують цим самі через data-bs-dismiss="modal"
// function closeModal() {
//   document.getElementById("modal-container").style.display = "none";
// }
