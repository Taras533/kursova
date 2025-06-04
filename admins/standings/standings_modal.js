function openStandingEditModal(data) {
  const modalHtml = `
    <div class="modal show fade" style="display:block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="edit_standing.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Редагування: ${data.team_name}</h5>
                        <button type="button" class="btn-close" onclick="closeModal()"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="${data.id}">
                        <input type="hidden" name="team_name" value="${data.team_name}">

                        <label>Перемоги (В): <input type="number" name="wins" class="form-control" value="${data.wins}" min="0" required></label>
                        <label>Нічиї (Н): <input type="number" name="draws" class="form-control" value="${data.draws}" min="0" required></label>
                        <label>Поразки (П): <input type="number" name="losses" class="form-control" value="${data.losses}" min="0" required></label>
                        <label>Забиті м'ячі (ЗМ): <input type="number" name="goals_for" class="form-control" value="${data.goals_for}" min="0" required></label>
                        <label>Пропущені м'ячі (ПМ): <input type="number" name="goals_against" class="form-control" value="${data.goals_against}" min="0" required></label>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Зберегти</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Скасувати</button>
                    </div>
                </form>
            </div>
        </div>
    </div>`;

  const container = document.getElementById("modal-container");
  container.innerHTML = modalHtml;
  container.style.display = "block";
}

function closeModal() {
  const container = document.getElementById("modal-container");
  container.innerHTML = "";
  container.style.display = "none";
}
